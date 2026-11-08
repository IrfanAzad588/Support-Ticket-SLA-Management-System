<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    const STATUS_OPEN = 'open';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_WAITING = 'waiting_for_customer';
    const STATUS_RESOLVED = 'resolved';
    const STATUS_CLOSED = 'closed';

    protected $fillable = [
        'ticket_number',
        'subject',
        'description',
        'category_id',
        'priority_id',
        'requester_id',
        'assigned_to',
        'status',
        'due_at',
        'assigned_at',
        'first_response_at',
        'resolved_at',
        'closed_at',
        'is_escalated',
    ];

    protected $casts = [
        'due_at' => 'datetime',
        'assigned_at' => 'datetime',
        'first_response_at' => 'datetime',
        'resolved_at' => 'datetime',
        'closed_at' => 'datetime',
        'is_escalated' => 'boolean',
    ];

    public function requester()
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function priority()
    {
        return $this->belongsTo(Priority::class);
    }

    public function slaRule()
    {
        return $this->hasOne(
            SlaRule::class,
            'priority_id',
            'priority_id'
        );
        // return $this->hasOneThrough(
        //     SlaRule::class,
        //     Priority::class,
        //     'id',
        //     'priority_id',
        //     'priority_id',
        //     'id'
        // );
    }

    public function replies()
    {
        return $this->hasMany(TicketReply::class);
    }

    public function publicReplies()
    {
        return $this->replies()->where('is_internal', false);
    }

    public function attachments()
    {
        return $this->hasMany(TicketAttachment::class);
    }

    public function activities()
    {
        return $this->hasMany(TicketActivity::class)
            ->latest();
    }

    public function escalations()
    {
        return $this->hasMany(TicketEscalation::class);
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', [
            self::STATUS_OPEN,
            self::STATUS_IN_PROGRESS,
            self::STATUS_WAITING,
        ]);
    }

    public function scopeResolved($query)
    {
        return $query->whereIn('status', [
            self::STATUS_RESOLVED,
            self::STATUS_CLOSED,
        ]);
    }
}
