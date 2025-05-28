<?php

namespace Database\Seeders;

use App\Models\Sla;
use Illuminate\Database\Seeder;

class SlaPolicySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $slas = [
            [
                'name' => 'Low Priority SLA',
                'response_time_hours' => 24,
                'resolution_time_hours' => 72,
                'priority' => 'low',
            ],
            [
                'name' => 'Medium Priority SLA',
                'response_time_hours' => 12,
                'resolution_time_hours' => 48,
                'priority' => 'medium',
            ],
            [
                'name' => 'High Priority SLA',
                'response_time_hours' => 4,
                'resolution_time_hours' => 24,
                'priority' => 'high',
            ],
            [
                'name' => 'Urgent Priority SLA',
                'response_time_hours' => 1,
                'resolution_time_hours' => 8,
                'priority' => 'urgent',
            ],
        ];

        foreach ($slas as $sla) {
            Sla::create($sla);
        }
    }
} 