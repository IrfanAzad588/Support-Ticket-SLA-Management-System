<?php

namespace App\Jobs;

use App\Models\Ticket;
use App\Models\TicketImport;
use App\Models\TicketImportError;
use App\Models\User;
use App\Services\TicketService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Throwable;

class ProcessTicketCsvImport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 3600;

    public function __construct(
        public int $importId
    ) {}

    public function handle(TicketService $ticketService): void
    {
        $import = TicketImport::findOrFail($this->importId);

        $import->update([
            'status' => 'processing',
            'started_at' => now(),
        ]);

        $stream = Storage::disk($import->disk)
            ->readStream($import->file_path);

        if (! $stream) {
            throw new \RuntimeException('Cannot open CSV file.');
        }

        $header = fgetcsv($stream);

        if (! $header) {
            fclose($stream);
            throw new \RuntimeException('CSV file is empty.');
        }

        // Remove UTF-8 BOM from first column if present.
        $header[0] = preg_replace('/^\xEF\xBB\xBF/', '', $header[0]);

        $processed = 0;
        $success = 0;
        $failed = 0;
        $rowNumber = 1;

        try {
            while (($values = fgetcsv($stream)) !== false) {

                $rowNumber++;

                $processed++;
                $row = array_combine($header, $values);

                try {
                    if ($row === false) {
                        throw new \RuntimeException(
                            'CSV column count does not match header.'
                        );
                    }

                    $validator = Validator::make($row, [
                        'requester_email' => [
                            'required',
                            'email',
                            'exists:users,email',
                        ],
                        'subject' => ['required', 'string', 'max:255'],
                        'description' => ['required', 'string'],
                        'category_id' => ['required', 'exists:categories,id'],
                        'priority_id' => ['required', 'exists:priorities,id'],
                    ]);

                    if ($validator->fails()) {
                        throw new \RuntimeException(
                            $validator->errors()->first()
                        );
                    }

                    $customer = User::where(
                        'email',
                        $row['requester_email']
                    )->where('role', 'customer')->first();

                    if (! $customer) {
                        throw new \RuntimeException(
                            'The requester is not a customer.'
                        );
                    }

                    $isDuplicate = Ticket::where('requester_id', $customer->id)
                        ->where('subject', $row['subject'])
                        ->where('description', $row['description'])
                        ->where('category_id', $row['category_id'])
                        ->where('priority_id', $row['priority_id'])
                        ->exists();

                    if ($isDuplicate) {
                        throw new \RuntimeException(
                            'Duplicate ticket skipped: matching requester, subject, description, category, and priority already exist.'
                        );
                    }

                    $ticketService->createTicket([
                        'subject' => $row['subject'],
                        'description' => $row['description'],
                        'category_id' => $row['category_id'],
                        'priority_id' => $row['priority_id'],
                    ], $customer);

                    $success++;
                } catch (Throwable $e) {

                    $failed++;

                    TicketImportError::updateOrCreate(
                        [
                            'ticket_import_id' => $import->id,
                            'row_number' => $rowNumber,
                        ],
                        [
                            'row_data' => is_array($row) ? $row : null,
                            'error_message' => $e->getMessage(),
                        ]
                    );
                }

                // Save progress every 100 rows.
                if ($processed % 100 === 0) {
                    $import->update([
                        'processed_rows' => $processed,
                        'success_rows' => $success,
                        'failed_rows' => $failed,
                    ]);
                }
            }
        } finally {
            fclose($stream);
        }

        $import->update([
            'status' => 'completed',
            'total_rows' => $processed,
            'processed_rows' => $processed,
            'success_rows' => $success,
            'failed_rows' => $failed,
            'completed_at' => now(),
            'failure_reason' => null,
        ]);
    }

    /**
     * Record a job-level failure that cannot be associated with one CSV row.
     */
    public function failed(Throwable $exception): void
    {
        TicketImport::whereKey($this->importId)->update([
            'status' => 'failed',
            'failure_reason' => $exception->getMessage(),
            'completed_at' => now(),
        ]);
    }
}
