<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTicketReplyRequest;
use App\Models\Ticket;
use App\Models\TicketActivity;
use App\Models\TicketReply;
use App\Notifications\TicketEventNotification;
use Illuminate\Support\Facades\DB;

class TicketReplyController extends Controller
{
    public function store(
        StoreTicketReplyRequest $request,
        Ticket $ticket
    ) {
        $this->authorize('reply', $ticket);

        $data = $request->validated();

        $isInternal = (bool) ($data['is_internal'] ?? false);

        if ($isInternal) {
            $this->authorize('internalNote', $ticket);
        }

        DB::transaction(function () use (
            $request,
            $ticket,
            $data,
            $isInternal
        ) {
            $ticket = Ticket::whereKey($ticket->id)
                ->lockForUpdate()
                ->firstOrFail();

            $reply = TicketReply::create([
                'ticket_id' => $ticket->id,
                'user_id' => auth()->id(),
                'body' => $data['body'],
                'is_internal' => $isInternal,
            ]);

            if (
                ! $isInternal && ! $ticket->first_response_at
                && auth()->user()->isAgent()
            ) {
                $ticket->first_response_at = now();
                $ticket->save();
            }

            foreach ($request->file('attachments', []) as $file) {
                $path = $file->store(
                    "ticket-attachments/{$ticket->id}",
                    'local'
                );

                $reply->attachments()->create([
                    'ticket_id' => $ticket->id,
                    'uploaded_by' => auth()->id(),
                    'disk' => 'local',
                    'file_path' => $path,
                    'original_name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getMimeType(),
                    'size' => $file->getSize(),
                ]);
            }

            TicketActivity::create([
                'ticket_id' => $ticket->id,
                'causer_id' => auth()->id(),
                'action' => $isInternal
                    ? 'internal_note_added'
                    : 'reply_added',
                'description' => $isInternal
                    ? 'An internal note was added.'
                    : 'A public reply was added.',
            ]);
        });

        if (! $isInternal && auth()->user()->isAgent()) {
            $ticket->requester->notify(new TicketEventNotification(
                $ticket,
                'New support reply',
                "A support agent replied to ticket {$ticket->ticket_number}."
            ));
        }

        if (! $isInternal && auth()->user()->isCustomer() && $ticket->assignee) {
            $ticket->assignee->notify(new TicketEventNotification(
                $ticket,
                'New customer reply',
                "The customer replied to ticket {$ticket->ticket_number}."
            ));
        }

        return back()->with('success', 'Reply submitted.');
    }
}
