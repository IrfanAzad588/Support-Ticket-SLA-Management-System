<?php

namespace App\Services;

use App\Models\SlaRule;
use App\Models\Ticket;
use App\Models\TicketActivity;
use App\Models\User;
use App\Notifications\TicketEventNotification;
use Illuminate\Support\Facades\DB;

class TicketService
{
    protected BusinessHoursService $businessHours;

    public function __construct(
        BusinessHoursService $businessHours
    ) {
        $this->businessHours = $businessHours;
    }

    public function createTicket(
        array $data,
        User $customer
    ): Ticket {
        return DB::transaction(function () use ($data, $customer) {

            $sla = SlaRule::where(
                'priority_id',
                $data['priority_id']
            )->where('is_active', true)->firstOrFail();

            $now = now();

            $dueAt = $this->businessHours->addBusinessMinutes(
                $now,
                $sla->resolution_minutes
            );

            $ticket = Ticket::create([
                'subject' => $data['subject'],
                'description' => $data['description'],
                'category_id' => $data['category_id'],
                'priority_id' => $data['priority_id'],
                'requester_id' => $customer->id,
                'status' => Ticket::STATUS_OPEN,
                'due_at' => $dueAt,
            ]);

            // Unique database ID makes the ticket number unique.
            $ticket->ticket_number = sprintf(
                'TKT-%s-%06d',
                $ticket->created_at->format('Y'),
                $ticket->id
            );

            $ticket->save();

            $this->recordActivity(
                $ticket,
                $customer,
                'created',
                'Ticket created.'
            );

            $customer->notify(new TicketEventNotification(
                $ticket,
                'Ticket created',
                "Your ticket {$ticket->ticket_number} was created successfully."
            ));

            return $ticket;
        });
    }

    public function changeStatus(
        Ticket $ticket,
        string $status,
        User $actor
    ): Ticket {
        return DB::transaction(function () use (
            $ticket,
            $status,
            $actor
        ) {
            $ticket = Ticket::whereKey($ticket->id)
                ->lockForUpdate()
                ->firstOrFail();

            $oldStatus = $ticket->status;

            $ticket->status = $status;

            if ($status === Ticket::STATUS_RESOLVED) {
                $ticket->resolved_at = now();
            }

            if ($status === Ticket::STATUS_CLOSED) {
                $ticket->closed_at = now();

                if (! $ticket->resolved_at) {
                    $ticket->resolved_at = now();
                }
            }

            $ticket->save();

            $this->recordActivity(
                $ticket,
                $actor,
                'status_changed',
                "Status changed from {$oldStatus} to {$status}."
            );

            if ($ticket->requester_id !== $actor->id) {
                $ticket->requester->notify(new TicketEventNotification(
                    $ticket,
                    'Ticket status updated',
                    "Your ticket {$ticket->ticket_number} is now ".str_replace('_', ' ', $status).'.'
                ));
            }

            return $ticket;
        });
    }

    public function recordActivity(
        Ticket $ticket,
        ?User $actor,
        string $action,
        string $description,
        ?array $oldValues = null,
        ?array $newValues = null
    ): void {
        TicketActivity::create([
            'ticket_id' => $ticket->id,
            'causer_id' => $actor?->id,
            'action' => $action,
            'description' => $description,
            'old_values' => $oldValues,
            'new_values' => $newValues,
        ]);
    }
}
