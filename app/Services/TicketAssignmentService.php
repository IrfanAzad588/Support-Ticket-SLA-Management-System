<?php

namespace App\Services;

use App\Models\Ticket;
use App\Models\TicketActivity;
use App\Models\User;
use App\Notifications\TicketEventNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TicketAssignmentService
{
    public function assignManually(
        Ticket $ticket,
        User $agent,
        User $admin
    ): Ticket {
        return DB::transaction(function () use (
            $ticket,
            $agent,
            $admin
        ) {
            $ticket = Ticket::whereKey($ticket->id)
                ->lockForUpdate()
                ->firstOrFail();

            if (! $agent->isAgent() || ! $agent->is_active) {
                throw ValidationException::withMessages([
                    'assigned_to' => 'Invalid agent.',
                ]);
            }

            $oldAgent = $ticket->assigned_to;

            $ticket->assigned_to = $agent->id;
            $ticket->assigned_at = now();

            if ($ticket->status === Ticket::STATUS_OPEN) {
                $ticket->status = Ticket::STATUS_IN_PROGRESS;
            }

            $ticket->save();

            TicketActivity::create([
                'ticket_id' => $ticket->id,
                'causer_id' => $admin->id,
                'action' => 'manually_assigned',
                'description' => "Ticket assigned to {$agent->name}.",
                'old_values' => ['assigned_to' => $oldAgent],
                'new_values' => ['assigned_to' => $agent->id],
            ]);

            $agent->notify(new TicketEventNotification(
                $ticket,
                'Ticket assigned',
                "Ticket {$ticket->ticket_number} has been assigned to you."
            ));

            return $ticket;
        });
    }

    public function accept(Ticket $ticket, User $agent): Ticket
    {
        return DB::transaction(function () use ($ticket, $agent) {

            $ticket = Ticket::whereKey($ticket->id)
                ->lockForUpdate()
                ->firstOrFail();

            if (! $agent->isAgent() || ! $agent->is_active) {
                throw ValidationException::withMessages([
                    'agent' => 'Invalid support agent.',
                ]);
            }

            if ($ticket->assigned_to !== null) {
                throw ValidationException::withMessages([
                    'ticket' => 'This ticket has already been assigned.',
                ]);
            }

            $ticket->assigned_to = $agent->id;
            $ticket->assigned_at = now();
            $ticket->status = Ticket::STATUS_IN_PROGRESS;

            $ticket->save();

            TicketActivity::create([
                'ticket_id' => $ticket->id,
                'causer_id' => $agent->id,
                'action' => 'assigned',
                'description' => "Ticket assigned to {$agent->name}.",
            ]);

            $agent->notify(new TicketEventNotification(
                $ticket,
                'Ticket assigned',
                "Ticket {$ticket->ticket_number} has been assigned to you."
            ));

            return $ticket;
        });
    }

    public function autoAssign(Ticket $ticket): Ticket
    {
        return DB::transaction(function () use ($ticket) {

            // Lock agent records while selecting the least-loaded agent.
            $agents = User::where('role', 'agent')
                ->where('is_active', true)
                ->orderBy('id')
                ->lockForUpdate()
                ->get();

            if ($agents->isEmpty()) {
                throw ValidationException::withMessages([
                    'ticket' => 'No available support agents.',
                ]);
            }

            $ticket = Ticket::whereKey($ticket->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($ticket->assigned_to !== null) {
                return $ticket;
            }

            $selectedAgent = $agents
                ->map(function ($agent) {
                    $agent->active_ticket_count = Ticket::active()
                        ->where('assigned_to', $agent->id)
                        ->count();

                    return $agent;
                })
                ->sortBy([
                    ['active_ticket_count', 'asc'],
                    ['id', 'asc'],
                ])
                ->first();

            $ticket->assigned_to = $selectedAgent->id;
            $ticket->assigned_at = now();
            $ticket->status = Ticket::STATUS_IN_PROGRESS;
            $ticket->save();

            TicketActivity::create([
                'ticket_id' => $ticket->id,
                'causer_id' => null,
                'action' => 'auto_assigned',
                'description' => "Automatically assigned to {$selectedAgent->name}.",
            ]);

            $selectedAgent->notify(new TicketEventNotification(
                $ticket,
                'Ticket assigned',
                "Ticket {$ticket->ticket_number} was automatically assigned to you."
            ));

            return $ticket;
        });
    }
}
