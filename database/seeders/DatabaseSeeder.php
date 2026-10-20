<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed sample users
        $users = [
            ['name' => 'Alex Rivera', 'email' => 'alex@example.com'],
            ['name' => 'Samantha Reed', 'email' => 'samantha@example.com'],
            ['name' => 'Michael Chen', 'email' => 'michael@example.com'],
            ['name' => 'Elena Rostova', 'email' => 'elena@example.com'],
            ['name' => 'David Kim', 'email' => 'david@example.com'],
        ];

        foreach ($users as $u) {
            User::firstOrCreate(
                ['email' => $u['email']],
                [
                    'name' => $u['name'],
                    'password' => Hash::make('password123'),
                ]
            );
        }

        // 2. Seed Default Pulse Threshold Rules
        $rules = [
            [
                'metric_key' => 'slow_requests',
                'label' => 'Slow HTTP Requests',
                'warning_threshold' => 10,
                'critical_threshold' => 25,
                'unit' => 'requests',
                'is_active' => true,
            ],
            [
                'metric_key' => 'exceptions',
                'label' => 'Application Exceptions',
                'warning_threshold' => 5,
                'critical_threshold' => 15,
                'unit' => 'errors',
                'is_active' => true,
            ],
            [
                'metric_key' => 'cache_misses',
                'label' => 'Cache Miss Spikes',
                'warning_threshold' => 20,
                'critical_threshold' => 50,
                'unit' => 'misses',
                'is_active' => true,
            ],
            [
                'metric_key' => 'slow_queries',
                'label' => 'Slow Database Queries (>1000ms)',
                'warning_threshold' => 5,
                'critical_threshold' => 12,
                'unit' => 'queries',
                'is_active' => true,
            ],
            [
                'metric_key' => 'memory',
                'label' => 'PHP Memory Peak',
                'warning_threshold' => 64,
                'critical_threshold' => 128,
                'unit' => 'MB',
                'is_active' => true,
            ],
        ];

        foreach ($rules as $rule) {
            DB::table('pulse_threshold_rules')->updateOrInsert(
                ['metric_key' => $rule['metric_key']],
                array_merge($rule, ['created_at' => now(), 'updated_at' => now()])
            );
        }
    }
}
