<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\TicketActivity;
use App\Models\TicketEscalation;
use App\Models\TicketImport;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Redirect users to their respective dashboards.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        if (!$user->is_active) {
            abort(403, 'Your account is inactive.');
        }

        return match ($user->role) {
            'admin' => redirect()->route('admin.dashboard'),
            'agent' => redirect()->route('agent.dashboard'),
            'customer' => redirect()->route('customer.dashboard'),
            default => abort(403, 'Invalid user role.'),
        };
    }

    /**
     * Admin dashboard.
     */
    public function admin()
    {
        $activeStatuses = [
            'open',
            'in_progress',
            'waiting_for_customer',
        ];

        $tickets = Ticket::query();
        

        $stats = [
            'total_tickets' => (clone $tickets)->count(),

            'open_tickets' => (clone $tickets)
                ->whereIn('status', $activeStatuses)
                ->count(),

            'unassigned_tickets' => (clone $tickets)
                ->whereNull('assigned_to')
                ->whereIn('status', $activeStatuses)
                ->count(),

            'critical_tickets' => (clone $tickets)
                ->whereHas('priority', function ($query) {
                    $query->where('name', 'Critical');
                })
                ->whereIn('status', $activeStatuses)
                ->count(),

            'sla_breached' => (clone $tickets)
                ->whereNotNull('due_at')
                ->where('due_at', '<', now())
                ->whereIn('status', $activeStatuses)
                ->count(),

            'sla_approaching' => (clone $tickets)
                ->whereNotNull('due_at')
                ->whereBetween('due_at', [
                    now(),
                    now()->addHour(),
                ])
                ->whereIn('status', $activeStatuses)
                ->count(),

            'resolved_tickets' => (clone $tickets)
                ->where('status', 'resolved')
                ->count(),

            'total_agents' => User::where('role', 'agent')->count(),

            'total_customers' => User::where('role', 'customer')->count(),
        ];

        $latestTickets = Ticket::with([
            'requester',
            'assignee',
            'priority',
            'category',
        ])
            ->latest()
            ->take(10)
            ->get();

        $agentWorkloads = User::where('role', 'agent')
            ->withCount(['assignedTickets as active_ticket_count' => fn ($query) => $query->active()])
            ->orderBy('active_ticket_count')
            ->orderBy('name')
            ->get();

        $recentEscalations = TicketEscalation::with('ticket.priority')
            ->latest('triggered_at')
            ->take(8)
            ->get();

        $recentActivities = TicketActivity::with(['ticket', 'causer'])
            ->latest()
            ->take(8)
            ->get();

        $recentImports = TicketImport::with('uploader')
            ->latest()
            ->take(5)
            ->get();

        $notifications = auth()->user()->notifications()->latest()->take(5)->get();

        return view('admin.dashboard', compact(
            'stats',
            'latestTickets',
            'agentWorkloads',
            'recentEscalations',
            'recentActivities',
            'recentImports',
            'notifications'
        ));
    }

    /**
     * Agent dashboard.
     */
    public function agent(Request $request)
    {
        $userId = $request->user()->id;

        $activeStatuses = [
            'open',
            'in_progress',
            'waiting_for_customer',
        ];

        $tickets = Ticket::where('assigned_to', $userId);

        $stats = [
            'total_tickets' => (clone $tickets)->count(),

            'open_tickets' => (clone $tickets)
                ->where('status', 'open')
                ->count(),

            'in_progress' => (clone $tickets)
                ->where('status', 'in_progress')
                ->count(),

            'waiting_for_customer' => (clone $tickets)
                ->where('status', 'waiting_for_customer')
                ->count(),

            'resolved_tickets' => (clone $tickets)
                ->where('status', 'resolved')
                ->count(),

            'critical_tickets' => (clone $tickets)
                ->whereHas('priority', function ($query) {
                    $query->where('name', 'Critical');
                })
                ->whereIn('status', $activeStatuses)
                ->count(),

            'sla_breached' => (clone $tickets)
                ->whereNotNull('due_at')
                ->where('due_at', '<', now())
                ->whereIn('status', $activeStatuses)
                ->count(),
        ];

        $latestTickets = Ticket::with([
            'requester',
            'priority',
            'category',
        ])
            ->where('assigned_to', $userId)
            ->latest()
            ->take(10)
            ->get();

        $availableTickets = Ticket::with(['requester', 'priority', 'category'])
            ->whereNull('assigned_to')
            ->active()
            ->latest()
            ->take(6)
            ->get();

        $slaWatchlist = Ticket::with('priority')
            ->where('assigned_to', $userId)
            ->active()
            ->whereNotNull('due_at')
            ->where('due_at', '<=', now()->addHour())
            ->orderBy('due_at')
            ->take(6)
            ->get();

        $recentActivities = \App\Models\TicketActivity::with(['ticket', 'causer'])
            ->whereHas('ticket', fn ($query) => $query->where('assigned_to', $userId))
            ->latest()
            ->take(6)
            ->get();

        $notifications = $request->user()->notifications()->latest()->take(6)->get();

        return view('agent.dashboard', compact(
            'stats',
            'latestTickets',
            'availableTickets',
            'slaWatchlist',
            'recentActivities',
            'notifications'
        ));
    }

    /**
     * Customer dashboard.
     */
    public function customer(Request $request)
    {
        $userId = $request->user()->id;

        $tickets = Ticket::where('requester_id', $userId);

        $stats = [
            'total_tickets' => (clone $tickets)->count(),

            'open_tickets' => (clone $tickets)
                ->where('status', 'open')
                ->count(),

            'in_progress' => (clone $tickets)
                ->where('status', 'in_progress')
                ->count(),

            'waiting_for_customer' => (clone $tickets)
                ->where('status', 'waiting_for_customer')
                ->count(),

            'resolved_tickets' => (clone $tickets)
                ->where('status', 'resolved')
                ->count(),
        ];

        $latestTickets = Ticket::with([
            'assignee',
            'priority',
            'category',
        ])
            ->where('requester_id', $userId)
            ->latest()
            ->take(10)
            ->get();

        $notifications = $request->user()->notifications()->latest()->take(6)->get();

        return view('customer.dashboard', compact(
            'stats',
            'latestTickets',
            'notifications'
        ));
    }
}
