<?php

namespace Database\Seeders;

use App\Models\SlaPolicy;
use Illuminate\Database\Seeder;

class SlaPolicySeeder extends Seeder
{
    public function run(): void
    {
        $policies = [
            ['priority' => 'Urgent', 'respond_hours' => 1,  'resolve_hours' => 4,  'escalate_minutes' => 15,  'is_active' => true],
            ['priority' => 'High',   'respond_hours' => 2,  'resolve_hours' => 8,  'escalate_minutes' => 30,  'is_active' => true],
            ['priority' => 'Medium', 'respond_hours' => 4,  'resolve_hours' => 24, 'escalate_minutes' => 60,  'is_active' => true],
            ['priority' => 'Low',    'respond_hours' => 8,  'resolve_hours' => 40, 'escalate_minutes' => 120, 'is_active' => true],
        ];

        foreach ($policies as $policy) {
            SlaPolicy::firstOrCreate(
                ['priority' => $policy['priority']],
                $policy
            );
        }
    }
}
