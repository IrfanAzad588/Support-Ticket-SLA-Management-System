<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SlaRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'priority_id',
        'resolution_minutes',
        'warning_percent',
        'is_active',
    ];

    public function priority()
    {
        return $this->belongsTo(Priority::class);
    }
}
