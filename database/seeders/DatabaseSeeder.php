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
        $this->call(ServiceSeeder::class);

        if (app()->environment('production') || ! config('app.seed_demo_data')) {
            return;
        }

        User::query()->updateOrCreate([
            'username' => 'manager',
        ], [
            'name' => 'Demo Manager',
            'email' => 'demo.manager@example.com',
            'password' => 'manager123',
            'role' => 'manager',
            'must_change_password' => true,
        ]);
    }
}
