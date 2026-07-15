<?php

namespace App\Models;

use App\Enums\AgentStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AgentStatusModel extends Model
{
    protected $table = 'agent_statuses';

    protected $fillable = [
        'user_id',
        'status',
        'last_online_at',
    ];

    protected function casts(): array
    {
        return [
            'status'         => AgentStatus::class,
            'last_online_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
