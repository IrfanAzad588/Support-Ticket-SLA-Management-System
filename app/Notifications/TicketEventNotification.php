<?php

namespace App\Notifications;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TicketEventNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Ticket $ticket,
        public string $event,
        public string $message
    ) {
        $this->afterCommit = true;
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject($this->event)
            ->greeting("Hello {$notifiable->name},")
            ->line($this->message)
            ->action(
                'View Ticket',
                route('tickets.show', $this->ticket)
            )
            ->line('Thank you for using our support system.');
    }

    public function toArray($notifiable): array
    {
        return [
            'ticket_id' => $this->ticket->id,
            'ticket_number' => $this->ticket->ticket_number,
            'event' => $this->event,
            'message' => $this->message,
        ];
    }
}
