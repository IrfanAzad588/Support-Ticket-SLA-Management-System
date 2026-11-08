<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketImportError extends Model
{
    use HasFactory;

    protected $casts = ['row_data' => 'array'];

     protected $fillable = [
        'ticket_import_id',
        'row_number',
        'row_data',
        'error_message',
    ];


    public function ticketImport()
    {
        return $this->belongsTo(TicketImport::class);
    }
}
