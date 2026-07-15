<?php

namespace App\Models;

use App\Enums\TicketPriority;
use App\Enums\TicketSource;
use App\Enums\TicketStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ticket extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'ticket_number',
        'requester_id',
        'agent_id',
        'category_id',
        'asset_id',
        'location_id',
        'subject',
        'description',
        'priority',
        'status',
        'source',
        'sla_policy_id',
        'sla_respond_at',
        'sla_resolve_at',
        'sla_paused_at',
        'sla_paused_total_minutes',
        'first_response_at',
        'resolved_at',
        'closed_at',
    ];

    protected function casts(): array
    {
        return [
            'priority'                 => TicketPriority::class,
            'status'                   => TicketStatus::class,
            'source'                   => TicketSource::class,
            'sla_respond_at'           => 'datetime',
            'sla_resolve_at'           => 'datetime',
            'sla_paused_at'            => 'datetime',
            'first_response_at'        => 'datetime',
            'resolved_at'              => 'datetime',
            'closed_at'                => 'datetime',
        ];
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(TicketCategory::class, 'category_id');
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function slaPolicy(): BelongsTo
    {
        return $this->belongsTo(SlaPolicy::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(TicketComment::class);
    }

    public function logs(): HasMany
    {
        return $this->hasMany(TicketLog::class);
    }

    public function slaPauses(): HasMany
    {
        return $this->hasMany(TicketSlaPause::class);
    }

    public function escalations(): HasMany
    {
        return $this->hasMany(TicketEscalation::class);
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (blank($term)) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($term) {
            $q->where('ticket_number', 'like', "%{$term}%")
              ->orWhere('subject', 'like', "%{$term}%")
              ->orWhere('description', 'like', "%{$term}%");
        });
    }

    public function scopeOfStatus(Builder $query, ?string $status): Builder
    {
        if (blank($status)) {
            return $query;
        }
        return $query->where('status', $status);
    }

    public function scopeOfPriority(Builder $query, ?string $priority): Builder
    {
        if (blank($priority)) {
            return $query;
        }
        return $query->where('priority', $priority);
    }

    public function scopeOfCategory(Builder $query, ?int $categoryId): Builder
    {
        if (blank($categoryId)) {
            return $query;
        }
        return $query->where('category_id', $categoryId);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereIn('status', TicketStatus::activeStatuses());
    }

    public function scopeMine(Builder $query): Builder
    {
        return $query->where('requester_id', auth()->id());
    }

    public function scopeAssignedToMe(Builder $query): Builder
    {
        return $query->where('agent_id', auth()->id());
    }

    public function isActive(): bool
    {
        return in_array($this->status, TicketStatus::activeStatuses());
    }
}
