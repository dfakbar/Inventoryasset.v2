<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TicketSlaPause extends Model
{
    protected $table = 'ticket_sla_pauses';

    protected $fillable = [
        'ticket_id',
        'paused_at',
        'resumed_at',
        'reason',
        'paused_by',
    ];

    protected function casts(): array
    {
        return [
            'paused_at'  => 'datetime',
            'resumed_at' => 'datetime',
        ];
    }

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    public function pausedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'paused_by');
    }
}
