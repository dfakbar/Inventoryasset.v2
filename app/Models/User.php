<?php

namespace App\Models;

use App\Enums\UserRole;
use Database\Factories\UserFactory;
use App\Models\AgentSpecialization;
use App\Models\AgentStatusModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'role'              => UserRole::class,
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === UserRole::Admin;
    }

    public function isStaff(): bool
    {
        return $this->role === UserRole::Staff;
    }

    public function isAgent(): bool
    {
        return $this->role === UserRole::Agent;
    }

    public function isRequester(): bool
    {
        return $this->isStaff() || $this->isAgent();
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'requester_id');
    }

    public function assignedTickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'agent_id');
    }

    public function agentStatus()
    {
        return $this->hasOne(AgentStatusModel::class, 'user_id');
    }

    public function specializations(): HasMany
    {
        return $this->hasMany(AgentSpecialization::class, 'user_id');
    }
}
