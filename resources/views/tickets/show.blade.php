<style>
    .ticket-details-wrapper {
        max-width: 1200px;
        margin: 0 auto;
    }

    .ticket-layout {
        display: grid;
        grid-template-columns: 320px minmax(0, 1fr);
        gap: 20px;
        align-items: start;
    }

    .ticket-panel {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.04);
        overflow: hidden;
    }

    .ticket-panel-header {
        padding: 16px 20px;
        border-bottom: 1px solid #e5e7eb;
        background: #ffffff;
    }

    .ticket-panel-header h5 {
        font-size: 16px;
        font-weight: 700;
        color: #1f2937;
        margin: 0;
    }

    .ticket-info-body {
        padding: 20px;
    }

    .ticket-number {
        color: #2563eb;
        font-size: 14px;
        font-weight: 700;
        margin-bottom: 10px;
    }

    .ticket-subject {
        font-size: 18px;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 12px;
        line-height: 1.5;
    }

    .ticket-description {
        font-size: 14px;
        color: #6b7280;
        line-height: 1.7;
        white-space: pre-wrap;
        overflow-wrap: anywhere;
    }

    .ticket-info-divider {
        border: 0;
        border-top: 1px solid #e5e7eb;
        margin: 18px 0;
    }

    .ticket-meta {
        margin-bottom: 18px;
    }

    .ticket-meta:last-child {
        margin-bottom: 0;
    }

    .ticket-meta-label {
        display: block;
        font-size: 12px;
        color: #9ca3af;
        margin-bottom: 5px;
    }

    .ticket-meta-value {
        font-size: 14px;
        font-weight: 600;
        color: #374151;
    }

    /* Conversation */

    .conversation-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 8px;
    }

    .message-count {
        font-size: 12px;
        color: #9ca3af;
    }

    .conversation-body {
        height: 380px;
        overflow-y: auto;
        padding: 20px;
        background: #f8fafc;
    }

    .conversation-body::-webkit-scrollbar {
        width: 6px;
    }

    .conversation-body::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 10px;
    }

    .chat-message {
        display: flex;
        flex-direction: column;
        margin-bottom: 18px;
    }

    .chat-message.mine {
        align-items: flex-end;
    }

    .chat-message.other {
        align-items: flex-start;
    }

    .chat-message-info {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 5px;
        font-size: 12px;
        color: #6b7280;
    }

    .chat-bubble {
        max-width: 80%;
        padding: 10px 14px;
        border-radius: 10px;
        font-size: 14px;
        line-height: 1.6;
        overflow-wrap: anywhere;
    }

    .chat-message.mine .chat-bubble {
        background: #2563eb;
        color: #ffffff;
        border-bottom-right-radius: 3px;
    }

    .chat-message.other .chat-bubble {
        background: #ffffff;
        color: #374151;
        border: 1px solid #e5e7eb;
        border-bottom-left-radius: 3px;
    }

    .chat-message.mine .chat-message-info {
        justify-content: flex-end;
    }

    .chat-message.other .chat-message-info {
        justify-content: flex-start;
    }

    .internal-note {
        display: inline-block;
        background: #fef3c7;
        color: #92400e;
        padding: 3px 8px;
        border-radius: 4px;
        font-size: 11px;
        font-weight: 600;
        margin-top: 8px;
    }

    .empty-conversation {
        display: flex;
        height: 100%;
        justify-content: center;
        align-items: center;
        color: #9ca3af;
        font-size: 14px;
    }

    /* Reply Form */

    .reply-section {
        padding: 16px 20px;
        background: #ffffff;
        border-top: 1px solid #e5e7eb;
    }

    .reply-textarea {
        display: block;
        width: 100%;
        min-height: 65px;
        padding: 10px 12px;
        border: 1px solid #d1d5db;
        border-radius: 7px;
        font-size: 13px;
        color: #374151;
        resize: vertical;
        outline: none;
        transition: 0.2s;
    }

    .reply-textarea:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.10);
    }

    .reply-bottom {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
        margin-top: 12px;
    }

    .reply-options {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .attachment-input {
        max-width: 250px;
        font-size: 12px;
        color: #6b7280;
    }

    .reply-attachments {
        margin-top: 10px;
        padding-top: 8px;
        border-top: 1px solid rgba(148, 163, 184, 0.35);
    }

    .reply-attachment {
        display: flex;
        align-items: center;
        gap: 6px;
        width: fit-content;
        max-width: 100%;
        color: inherit;
        font-size: 12px;
        line-height: 1.5;
        text-decoration: underline;
        overflow-wrap: anywhere;
    }

    .chat-message.other .reply-attachment {
        color: #2563eb;
    }

    .attachment-preview {
        display: block;
        width: 120px;
        height: 90px;
        margin-top: 8px;
        border: 1px solid rgba(148, 163, 184, 0.45);
        border-radius: 6px;
        cursor: zoom-in;
        object-fit: cover;
    }

    .attachment-modal {
        display: none;
        position: fixed;
        z-index: 1000;
        inset: 0;
        align-items: center;
        justify-content: center;
        padding: 24px;
        background: rgba(15, 23, 42, 0.82);
        cursor: zoom-out;
    }

    .attachment-modal.is-open {
        display: flex;
    }

    .attachment-modal img {
        max-width: 95vw;
        max-height: 90vh;
        border-radius: 8px;
        object-fit: contain;
        cursor: default;
    }

    .internal-checkbox {
        display: flex;
        align-items: center;
        gap: 7px;
        font-size: 12px;
        color: #92400e;
        cursor: pointer;
    }

    .reply-submit {
        background: #2563eb;
        color: #ffffff;
        border: none;
        border-radius: 6px;
        padding: 9px 22px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.2s;
    }

    .reply-submit:hover {
        background: #1d4ed8;
    }

    .reply-error {
        color: #dc2626;
        font-size: 12px;
        margin-top: 5px;
    }

    @media (max-width: 768px) {
        .ticket-layout {
            grid-template-columns: 1fr;
        }

        .conversation-body {
            height: 320px;
        }

        .chat-bubble {
            max-width: 90%;
        }
    }

    @media (max-width: 480px) {

        .ticket-info-body,
        .conversation-body {
            padding: 14px;
        }

        .reply-section {
            padding: 14px;
        }

        .reply-bottom {
            align-items: flex-start;
        }

        .reply-submit {
            width: 100%;
        }
    }
</style>

<x-app-layout>

    {{-- Page Header --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Ticket Details
        </h2>
    </x-slot>

    <div class="py-8 px-4" style="margin-top: 20px;">

        <div class="ticket-details-wrapper">

            <div class="ticket-layout">

                {{-- Ticket Information - Left Side --}}
                <div class="ticket-panel">

                    <div class="ticket-panel-header">
                        <h5>Ticket Information</h5>
                    </div>

                    <div class="ticket-info-body">

                        <div class="ticket-number">
                            {{ $ticket->ticket_number }}
                        </div>

                        <h3 class="ticket-subject">
                            {{ $ticket->subject }}
                        </h3>

                        <p class="ticket-description">
                            {{ $ticket->description }}
                        </p>

                        <hr class="ticket-info-divider">

                        <div class="ticket-meta">
                            <span class="ticket-meta-label">Priority</span>
                            <span class="ticket-meta-value">
                                {{ $ticket->priority?->name ?? 'N/A' }}
                            </span>
                        </div>

                        @if(auth()->user()->isAgent() && !$ticket->assigned_to && !in_array($ticket->status, ['resolved', 'closed']))
                        <form method="POST" action="{{ route('tickets.accept', $ticket) }}" class="mb-4">
                            @csrf
                            <button class="w-full rounded bg-blue-600 px-3 py-2 text-sm font-semibold text-white">Accept this ticket</button>
                        </form>
                        @endif

                        @if(auth()->user()->isAgent() && $ticket->assigned_to === auth()->id() && !in_array($ticket->status, ['resolved', 'closed']))
                        <div class="ticket-meta">
                            <span class="ticket-meta-label">Update Status</span>
                            <form method="POST" action="{{ route('tickets.status.update', $ticket) }}" class="mt-2 flex gap-2">
                                @csrf
                                @method('PATCH')
                                <select name="status" class="min-w-0 flex-1 rounded border-gray-300 text-sm">
                                    <option value="in_progress" @selected($ticket->status === 'in_progress')>In Progress</option>
                                    <option value="waiting_for_customer" @selected($ticket->status === 'waiting_for_customer')>Waiting for Customer</option>
                                    <option value="resolved">Resolved</option>
                                    <option value="closed">Closed</option>
                                </select>
                                <button class="rounded bg-gray-800 px-3 py-2 text-xs text-white">Save</button>
                            </form>
                        </div>
                        @endif

                        @if(auth()->user()->isAgent() && $ticket->assigned_to === auth()->id() && $ticket->status === 'resolved')
                        <form method="POST" action="{{ route('tickets.status.update', $ticket) }}" class="mb-4">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="closed">
                            <button class="w-full rounded bg-gray-700 px-3 py-2 text-sm font-semibold text-white">Close ticket</button>
                        </form>
                        @endif

                        <div class="ticket-meta">
                            <span class="ticket-meta-label">Status</span>
                            <span class="ticket-meta-value">
                                {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                            </span>
                        </div>

                        <div class="ticket-meta">
                            <span class="ticket-meta-label">Assigned Agent</span>
                            <span class="ticket-meta-value">
                                {{ $ticket->assignee?->name ?? 'Unassigned' }}
                            </span>
                        </div>

                        @can('reopen', $ticket)
                        <form method="POST" action="{{ route('tickets.reopen-request', $ticket) }}" class="mt-4">
                            @csrf
                            <button class="text-sm font-semibold text-blue-600">Request ticket reopening</button>
                        </form>
                        @endcan

                        @can('assign', $ticket)
                        <hr class="ticket-info-divider">
                        <div class="ticket-meta">
                            <span class="ticket-meta-label">Assignment</span>
                            <form method="POST" action="{{ route('tickets.assign', $ticket) }}" class="mt-2">
                                @csrf
                                <select name="assigned_to" class="w-full rounded border-gray-300 text-sm" required>
                                    <option value="">Select an agent</option>
                                    @foreach($agents as $agent)
                                    <option value="{{ $agent->id }}" @selected($ticket->assigned_to === $agent->id)>{{ $agent->name }}</option>
                                    @endforeach
                                </select>
                                <button class="mt-2 px-3 py-2 text-xs rounded bg-blue-600 text-white">Assign agent</button>
                            </form>
                            @if(!$ticket->assigned_to)
                            <form method="POST" action="{{ route('tickets.auto-assign', $ticket) }}" class="mt-2">@csrf<button class="text-xs text-blue-600">Auto-assign least-loaded agent</button></form>
                            @endif
                        </div>
                        @endcan

                        @can('update', $ticket)
                        @if(!in_array($ticket->status, ['resolved', 'closed']))
                        <form method="POST" action="{{ route('tickets.resolve', $ticket) }}">@csrf<button class="text-sm text-green-700 font-semibold">Mark ticket resolved</button></form>
                        @endif
                        @endcan

                    </div>

                </div>

                    @if(auth()->user()->isAgent())
                    <div class="ticket-panel mt-5">
                        <div class="ticket-panel-header"><h5>Activity History</h5></div>
                        <div class="ticket-info-body">
                            @forelse($ticket->activities as $activity)
                            <div class="mb-3 text-sm last:mb-0">
                                <p class="text-gray-700">{{ $activity->description }}</p>
                                <p class="text-xs text-gray-500">{{ $activity->causer?->name ?? 'System' }} · {{ $activity->created_at->format('d M, h:i A') }}</p>
                            </div>
                            @empty
                            <p class="text-sm text-gray-500">No activity recorded.</p>
                            @endforelse
                        </div>
                    </div>
                    @endif


                {{-- Conversation and Reply - Right Side --}}
                <div class="ticket-panel">

                    {{-- Conversation Header --}}
                    <div class="ticket-panel-header conversation-header">

                        <h5>Conversation</h5>

                        <span class="message-count">
                            {{ $ticket->replies->count() }} messages
                        </span>

                    </div>


                    {{-- Conversation Messages --}}
                    <div class="conversation-body">

                        @forelse($ticket->replies as $reply)

                        @php
                        $isMine = $reply->user_id === auth()->id();
                        @endphp

                        <div class="chat-message {{ $isMine ? 'mine' : 'other' }}">

                            <div class="chat-message-info">

                                <strong>
                                    {{ $reply->user?->name ?? 'Deleted user' }}
                                </strong>

                                <span>
                                    {{ $reply->created_at->format('d M, h:i A') }}
                                </span>

                            </div>

                            <div class="chat-bubble">

                                <p style="white-space: pre-wrap; margin: 0;">{{ $reply->body }}</p>

                                @if($reply->is_internal)
                                <span class="internal-note">
                                    Internal Note
                                </span>
                                @endif

                                @if($reply->attachments->isNotEmpty())
                                <div class="reply-attachments">
                                    @foreach($reply->attachments as $attachment)
                                    @if(str_starts_with((string) $attachment->mime_type, 'image/'))
                                    <img
                                        class="attachment-preview"
                                        src="{{ route('attachments.preview', $attachment) }}"
                                        alt="{{ $attachment->original_name }}"
                                        data-full-image="{{ route('attachments.preview', $attachment) }}">
                                    @else
                                    <a
                                        class="reply-attachment"
                                        href="{{ route('attachments.download', $attachment) }}">
                                        <span aria-hidden="true">&#128206;</span>
                                        <span>{{ $attachment->original_name }}</span>
                                    </a>
                                    @endif
                                    @endforeach
                                </div>
                                @endif

                            </div>

                        </div>

                        @empty

                        <div class="empty-conversation">
                            No messages yet.
                        </div>

                        @endforelse

                    </div>


                    {{-- Reply Form --}}
                    @can('reply', $ticket)

                    <div class="reply-section">

                        <form method="POST"
                            action="{{ route('tickets.replies.store', $ticket) }}"
                            enctype="multipart/form-data">

                            @csrf

                            {{-- Message Input --}}
                            <div>
                                <textarea
                                    name="body"
                                    class="reply-textarea"
                                    rows="2"
                                    placeholder="Write your reply..."
                                    required>{{ old('body') }}</textarea>

                                @error('body')
                                <p class="reply-error">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Reply Options --}}
                            <div class="reply-bottom">

                                <div class="reply-options">

                                    <input
                                        type="file"
                                        name="attachments[]"
                                        class="attachment-input"
                                        multiple>

                                    @can('internalNote', $ticket)

                                    <label class="internal-checkbox">

                                        <input
                                            type="checkbox"
                                            name="is_internal"
                                            value="1"
                                            id="internalNote"
                                            {{ old('is_internal') ? 'checked' : '' }}>

                                        Internal Note

                                    </label>

                                    @endcan

                                </div>


                                {{-- Send Reply --}}
                                <button type="submit" class="reply-submit">
                                    Send Reply
                                </button>

                            </div>

                        </form>

                    </div>

                    @endcan

                </div>

            </div>

        </div>

    </div>

    <div class="attachment-modal" id="attachmentModal" aria-hidden="true">
        <img src="" alt="Attachment preview" id="attachmentModalImage">
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const modal = document.getElementById('attachmentModal');
            const modalImage = document.getElementById('attachmentModalImage');

            document.querySelectorAll('.attachment-preview').forEach(function (preview) {
                preview.addEventListener('click', function () {
                    modalImage.src = preview.dataset.fullImage;
                    modalImage.alt = preview.alt;
                    modal.classList.add('is-open');
                    modal.setAttribute('aria-hidden', 'false');
                });
            });

            modal.addEventListener('click', function (event) {
                if (event.target === modal) {
                    modal.classList.remove('is-open');
                    modal.setAttribute('aria-hidden', 'true');
                    modalImage.src = '';
                }
            });
        });
    </script>

</x-app-layout>
