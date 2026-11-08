<?php

namespace App\Http\Controllers;

use App\Models\TicketAttachment;
use Illuminate\Http\Request;

class TicketAttachmentController extends Controller
{
    public function download(TicketAttachment $attachment)
    {
        $this->authorizeAttachmentAccess($attachment);

        abort_unless(
            \Illuminate\Support\Facades\Storage::disk($attachment->disk)
                ->exists($attachment->file_path),
            404
        );

        return \Illuminate\Support\Facades\Storage::disk($attachment->disk)
            ->download(
                $attachment->file_path,
                $attachment->original_name
            );
    }

    public function preview(TicketAttachment $attachment)
    {
        $this->authorizeAttachmentAccess($attachment);

        abort_unless(
            str_starts_with((string) $attachment->mime_type, 'image/'),
            404
        );

        $disk = \Illuminate\Support\Facades\Storage::disk($attachment->disk);

        abort_unless($disk->exists($attachment->file_path), 404);

        return $disk->response(
            $attachment->file_path,
            $attachment->original_name,
            ['Content-Type' => $attachment->mime_type],
            'inline'
        );
    }

    private function authorizeAttachmentAccess(TicketAttachment $attachment): void
    {
        $this->authorize('view', $attachment->ticket);

        abort_if(
            auth()->user()->isCustomer() && $attachment->reply?->is_internal,
            403
        );
    }
}
