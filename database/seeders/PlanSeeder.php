<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Free',
                'code' => 'free',
                'price' => 0.00,
                'limits' => json_encode([
                    'users' => 1,
                    'storage' => '1GB',
                    'features' => ['basic_reports']
                ]),
                'is_active' => true,
            ],
            [
                'name' => 'Pro',
                'code' => 'pro',
                'price' => 49.90,
                'limits' => json_encode([
                    'users' => 5,
                    'storage' => '10GB',
                    'features' => ['advanced_reports', 'api_access', 'priority_support']
                ]),
                'is_active' => true,
            ],
            [
                'name' => 'Enterprise',
                'code' => 'enterprise',
                'price' => 199.90,
                'limits' => json_encode([
                    'users' => 999,
                    'storage' => '1TB',
                    'features' => ['all_features', 'dedicated_manager', 'sso']
                ]),
                'is_active' => true,
            ],
        ];

        foreach ($plans as $plan) {
            Plan::updateOrCreate(['code' => $plan['code']], $plan);
        }
    }
}
