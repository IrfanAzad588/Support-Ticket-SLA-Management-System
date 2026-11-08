<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketEscalation extends Model
{
    use HasFactory;

    protected $casts = ['triggered_at' => 'datetime', 'notified_at' => 'datetime', 'details' => 'array'];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }
}
