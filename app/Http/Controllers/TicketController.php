<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTicketRequest;
use App\Models\Category;
use App\Models\Priority;
use App\Models\SlaRule;
use App\Models\Ticket;
use App\Models\TicketActivity;
use App\Models\User;
use App\Notifications\TicketEventNotification;
use App\Services\TicketAssignmentService;
use App\Services\TicketService;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function __construct(
        private TicketService $ticketService,
        private TicketAssignmentService $assignmentService
    ) {}

    public function index(Request $request)
    {
        $user = auth()->user();

        $query = Ticket::with([
            'requester:id,name',
            'assignee:id,name',
            'category:id,name',
            'priority:id,name',
        ]);

        // Customer sees only their tickets.
        if ($user->isCustomer()) {
            $query->where('requester_id', $user->id);
        }

        // Agent sees assigned and unassigned tickets.
        if ($user->isAgent()) {
            $query->where(function ($q) use ($user) {
                $q->where('assigned_to', $user->id)
                    ->orWhereNull('assigned_to');
            });
        }

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('ticket_number', 'like', "%{$search}%")
                    ->orWhere('subject', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority_id')) {
            $query->where('priority_id', $request->priority_id);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($user->isAdmin() && $request->filled('assigned_to')) {
            $query->where('assigned_to', $request->assigned_to);
        }

        if ($user->isAdmin() && $request->filled('requester_id')) {
            $query->where('requester_id', $request->requester_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('sla_status')) {
            $query->active()->whereNotNull('due_at');

            if ($request->sla_status === 'breached') {
                $query->where('due_at', '<=', now());
            }

            if (in_array($request->sla_status, ['approaching', 'within_sla'], true)) {
                $rules = SlaRule::where('is_active', true)->get();

                $query->where(function ($slaQuery) use ($rules, $request) {
                    foreach ($rules as $rule) {
                        $warningAt = now()->addMinutes((int) ceil(
                            $rule->resolution_minutes * $rule->warning_percent / 100
                        ));

                        $slaQuery->orWhere(function ($priorityQuery) use ($rule, $warningAt, $request) {
                            $priorityQuery->where('priority_id', $rule->priority_id);

                            if ($request->sla_status === 'approaching') {
                                $priorityQuery->where('due_at', '>', now())
                                    ->where('due_at', '<=', $warningAt);
                            } else {
                                $priorityQuery->where('due_at', '>', $warningAt);
                            }
                        });
                    }
                });
            }
        }

        $tickets = $query
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $priorities = Priority::where('is_active', true)->orderBy('level')->get(['id', 'name']);
        $categories = Category::where('is_active', true)->orderBy('name')->get(['id', 'name']);
        $agents = $user->isAdmin()
            ? User::where('role', 'agent')->where('is_active', true)->orderBy('name')->get(['id', 'name'])
            : collect();
        $customers = $user->isAdmin()
            ? User::where('role', 'customer')->where('is_active', true)->orderBy('name')->get(['id', 'name'])
            : collect();

        return view('tickets.index', compact('tickets', 'priorities', 'categories', 'agents', 'customers'));
    }

    public function create()
    {
        $this->authorize('create', Ticket::class);

        $categories = Category::where('is_active', true)->get();
        $priorities = Priority::where('is_active', true)->orderBy('level')->get();

        return view(
            'tickets.create',
            compact('categories', 'priorities')
        );
    }

    public function store(StoreTicketRequest $request)
    {
        $user = auth()->user();

        $ticket = $this->ticketService->createTicket(
            $request->validated(),
            $user
        );

        return redirect()
            ->route($user->isAdmin() ? 'tickets.index' : 'customer.dashboard')
            ->with('success', 'Ticket created successfully.');
    }

    public function show(Ticket $ticket)
    {
        $this->authorize('view', $ticket);

        $ticket->load([
            'requester',
            'assignee',
            'category',
            'priority',
            'replies.user',
            'replies.attachments',
            'activities.causer',
            'attachments',
        ]);

        // Crucially, filter internal notes server-side.
        if (auth()->user()->isCustomer()) {
            $ticket->setRelation(
                'replies',
                $ticket->replies
                    ->where('is_internal', false)
                    ->values()
            );
        }

        $agents = auth()->user()->isAdmin()
            ? User::where('role', 'agent')->where('is_active', true)->orderBy('name')->get()
            : collect();

        return view('tickets.show', compact('ticket', 'agents'));
    }

    public function accept(Ticket $ticket)
    {
        $this->authorize('view', $ticket);

        $this->assignmentService->accept(
            $ticket,
            auth()->user()
        );

        return back()->with('success', 'Ticket assigned to you.');
    }

    public function assign(Request $request, Ticket $ticket)
    {
        $this->authorize('assign', $ticket);

        $validated = $request->validate([
            'assigned_to' => [
                'required',
                'exists:users,id',
            ],
        ]);

        $agent = User::where('id', $validated['assigned_to'])
            ->where('role', 'agent')
            ->where('is_active', true)
            ->firstOrFail();

        // Implement manual assignment with the same row-locking
        // approach used in TicketAssignmentService::accept().
        $this->assignmentService->assignManually($ticket, $agent, auth()->user());

        return back()->with('success', 'Ticket assigned successfully.');
    }

    public function autoAssign(Ticket $ticket)
    {
        $this->authorize('assign', $ticket);

        $this->assignmentService->autoAssign($ticket);

        return back()->with('success', 'Ticket automatically assigned to the least-loaded agent.');
    }

    public function resolve(Ticket $ticket)
    {
        $this->authorize('update', $ticket);

        $this->ticketService->changeStatus($ticket, Ticket::STATUS_RESOLVED, auth()->user());

        return back()->with('success', 'Ticket resolved.');
    }

    public function updateStatus(Request $request, Ticket $ticket)
    {
        $this->authorize('update', $ticket);

        $data = $request->validate([
            'status' => ['required', 'in:in_progress,waiting_for_customer,resolved,closed'],
        ]);

        $this->ticketService->changeStatus($ticket, $data['status'], auth()->user());

        return back()->with('success', 'Ticket status updated.');
    }

    public function requestReopen(Ticket $ticket)
    {
        $this->authorize('reopen', $ticket);

        TicketActivity::create([
            'ticket_id' => $ticket->id,
            'causer_id' => auth()->id(),
            'action' => 'reopen_requested',
            'description' => 'The customer requested that this resolved ticket be reopened.',
        ]);

        $recipients = collect([$ticket->assignee])
            ->merge(User::where('role', 'admin')->where('is_active', true)->get())
            ->filter()
            ->unique('id');

        foreach ($recipients as $recipient) {
            $recipient->notify(new TicketEventNotification(
                $ticket,
                'Ticket reopen requested',
                "The customer requested reopening ticket {$ticket->ticket_number}."
            ));
        }

        return back()->with('success', 'Your reopen request has been sent to support.');
    }
}
