<?php

namespace App\Console\Commands;

use App\Models\Ticket;
use App\Models\TicketEscalation;
use App\Models\TicketActivity;
use App\Notifications\TicketEventNotification;
use Illuminate\Console\Command;

class MonitorTicketSla extends Command
{
    protected $signature = 'tickets:monitor-sla';

    protected $description = 'Monitor ticket SLA deadlines';

    public function handle(): int
    {
        Ticket::active()
            ->whereNotNull('due_at')
            ->with(['priority.slaRule', 'requester', 'assignee'])
            ->chunkById(200, function ($tickets) {

                foreach ($tickets as $ticket) {

                    $rule = $ticket->priority->slaRule;

                    if (!$rule || !$rule->is_active) {
                        continue;
                    }

                    $now = now();

                    $warningMinutes = (int) ceil(
                        $rule->resolution_minutes
                            * $rule->warning_percent / 100
                    );

                    $approachingAt = $ticket->due_at
                        ->copy()
                        ->subMinutes($warningMinutes);

                    $type = null;

                    if ($now->gte($ticket->due_at)) {
                        $type = 'breached';
                    } elseif ($now->gte($approachingAt)) {
                        $type = 'approaching';
                    }

                    if (!$type) {
                        continue;
                    }

                    // Unique ticket_id + type prevents duplicate events.
                    $escalation = TicketEscalation::firstOrCreate(
                        [
                            'ticket_id' => $ticket->id,
                            'type' => $type,
                        ],
                        [
                            'triggered_at' => now(),
                            'details' => [
                                'ticket_number' => $ticket->ticket_number,
                                'due_at' => $ticket->due_at->toDateTimeString(),
                            ],
                        ]
                    );

                    if (!$escalation->wasRecentlyCreated) {
                        continue;
                    }

                    $ticket->update(['is_escalated' => true]);

                    TicketActivity::create([
                        'ticket_id' => $ticket->id,
                        'causer_id' => null,
                        'action' => "sla_{$type}",
                        'description' => "Ticket SLA {$type}.",
                    ]);

                    // Send to the agent and customer.
                    $recipients = collect([
                        $ticket->assignee,
                        $ticket->requester,
                    ])->filter()->unique('id');

                    foreach ($recipients as $recipient) {
                        $recipient->notify(
                            new TicketEventNotification(
                                $ticket,
                                "SLA {$type}",
                                "Ticket {$ticket->ticket_number} is SLA {$type}."
                            )
                        );
                    }

                    $escalation->update([
                        'notified_at' => now(),
                    ]);
                }
            });

        $this->info('SLA monitoring completed.');

        return self::SUCCESS;
    }
}
