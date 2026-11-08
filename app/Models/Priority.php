<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Priority extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'level',
        'color',
        'is_active',
    ];

    public function slaRule()
    {
        return $this->hasOne(SlaRule::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }
}
