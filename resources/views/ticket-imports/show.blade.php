<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800">CSV Import</h2></x-slot>

    <div class="py-8"><div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        <div class="bg-white border rounded-lg p-5">
            <div class="flex justify-between gap-4 flex-wrap">
                <div><h3 class="font-semibold">{{ $ticketImport->original_file_name }}</h3><p class="text-sm text-gray-500">Uploaded by {{ $ticketImport->uploader?->name ?? 'Unknown' }}</p></div>
                <span class="px-3 py-1 rounded-full bg-gray-100 text-sm capitalize">{{ $ticketImport->status }}</span>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-5 text-sm">
                <div><span class="block text-gray-500">Total</span><strong>{{ $ticketImport->total_rows }}</strong></div>
                <div><span class="block text-gray-500">Processed</span><strong>{{ $ticketImport->processed_rows }}</strong></div>
                <div><span class="block text-gray-500">Succeeded</span><strong>{{ $ticketImport->success_rows }}</strong></div>
                <div><span class="block text-gray-500">Failed</span><strong>{{ $ticketImport->failed_rows }}</strong></div>
            </div>
            @if($ticketImport->failure_reason)
                <p class="mt-4 text-sm text-red-700"><strong>Import failed:</strong> {{ $ticketImport->failure_reason }}</p>
            @endif
        </div>
        <div class="bg-white border rounded-lg p-5"><h3 class="font-semibold mb-4">Import errors</h3>
            @forelse($ticketImport->errors as $error)
                <div class="border-b py-3 text-sm"><strong>Row {{ $error->row_number }}:</strong> {{ $error->error_message }}</div>
            @empty <p class="text-sm text-gray-500">No import errors recorded.</p> @endforelse
        </div>
    </div></div>
</x-app-layout>
