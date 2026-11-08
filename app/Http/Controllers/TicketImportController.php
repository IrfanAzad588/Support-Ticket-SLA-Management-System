<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessTicketCsvImport;
use App\Models\TicketImport;
use Illuminate\Http\Request;

class TicketImportController extends Controller
{
    public function show(TicketImport $ticketImport)
    {
        $this->authorize('import', \App\Models\Ticket::class);

        $ticketImport->load(['uploader', 'errors']);

        return view('ticket-imports.show', compact('ticketImport'));
    }

    public function store(Request $request)
    {
        $this->authorize('import', \App\Models\Ticket::class);

        $request->validate([
            'file' => [
                'required',
                'file',
                'mimes:csv,txt',
                'max:20480',
            ],
        ]);

        $file = $request->file('file');

        $path = $file->store('ticket-imports', 'local');

        $import = TicketImport::create([
            'uploaded_by' => auth()->id(),
            'disk' => 'local',
            'file_path' => $path,
            'original_file_name' => $file->getClientOriginalName(),
            'status' => 'pending',
        ]);

        ProcessTicketCsvImport::dispatch($import->id);

        return redirect()
            ->route('ticket-imports.show', $import)
            ->with('success', 'CSV import has started.');
    }
}
