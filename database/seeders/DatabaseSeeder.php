<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $adminTenant = \App\Models\Tenant::updateOrCreate(['name' => 'ArqAI Admin']);

        User::updateOrCreate(
            ['email' => 'admin@arqai.com'],
            [
                'name' => 'Super Admin',
                'password' => \Illuminate\Support\Facades\Hash::make('admin123'),
                'tenant_id' => $adminTenant->id,
                'is_admin' => true,
                'credits' => 9999,
                'status' => 'active',
            ]
        );
        
        // Also create a test user
        $testTenant = \App\Models\Tenant::updateOrCreate(['name' => 'Design Studio Test']);
        
        User::updateOrCreate(
            ['email' => 'user@test.com'],
            [
                'name' => 'Test User',
                'password' => \Illuminate\Support\Facades\Hash::make('user123'),
                'tenant_id' => $testTenant->id,
                'is_admin' => false,
                'credits' => 10,
                'status' => 'active',
            ]
        );

        // Clear existing plans
        \Illuminate\Support\Facades\DB::table('plans')->truncate();

        // Create 3 credit-based plans
        $plans = [
            ['name' => 'Starter', 'monthly_price' => 47.00, 'image_limit' => 10],
            ['name' => 'Pro', 'monthly_price' => 97.00, 'image_limit' => 30],
            ['name' => 'Expert', 'monthly_price' => 197.00, 'image_limit' => 100],
        ];

        foreach ($plans as $plan) {
            \App\Models\Plan::create($plan);
        }
    }
}
