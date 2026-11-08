<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketImport extends Model
{
    use HasFactory;

    protected $fillable = [

        'uploaded_by',
        'disk',
        'file_path',
        'original_file_name',
        'status',
        'total_rows',
        'processed_rows',
        'success_rows',
        'failed_rows',
        'started_at',
        'completed_at',
        'failure_reason',
    ];

    public function errors()
    {
        return $this->hasMany(TicketImportError::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
