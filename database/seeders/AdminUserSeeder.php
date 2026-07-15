<?php

namespace Database\Seeders;

use App\Enums\AgentStatus;
use App\Enums\UserRole;
use App\Models\AgentStatusModel;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\PermissionRegistrar;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // ── Super Admin ──
        $admin = User::updateOrCreate(
            ['email' => 'admin@company.com'],
            [
                'name'     => 'Super Admin',
                'password' => Hash::make('password123'),
                'role'     => UserRole::Admin->value,
            ]
        );
        // Assign Spatie role → admin role punya semua permission (via PermissionSeeder)
        $admin->syncRoles([UserRole::Admin->value]);

        // ── Staff (contoh, permission minimal) ──
        $staff = User::updateOrCreate(
            ['email' => 'staff@company.com'],
            [
                'name'     => 'Staff Operasional',
                'password' => Hash::make('password123'),
                'role'     => UserRole::Staff->value,
            ]
        );
        $staff->syncRoles([UserRole::Staff->value]);
        // Berikan permission view-only sebagai default untuk staff contoh
        $staff->syncPermissions(['asset.viewAny']);

        // ── Agent (contoh IT Support) ──
        $agent = User::updateOrCreate(
            ['email' => 'agent@company.com'],
            [
                'name'     => 'IT Support Agent',
                'password' => Hash::make('password123'),
                'role'     => UserRole::Agent->value,
            ]
        );
        $agent->syncRoles([UserRole::Agent->value]);
        $agent->syncPermissions(['ticket.viewAny', 'ticket.create', 'ticket.manage']);

        // Buat status agent
        AgentStatusModel::firstOrCreate(
            ['user_id' => $agent->id],
            ['status' => AgentStatus::Available->value, 'last_online_at' => now()]
        );
    }
}
