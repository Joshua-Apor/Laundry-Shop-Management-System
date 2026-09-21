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
        User::query()->updateOrCreate([
            'username' => 'josh',
        ], [
            'name' => 'Josh',
            'password' => 'employee123',
            'role' => 'employee',
        ]);

        User::query()->updateOrCreate([
            'username' => 'manager',
        ], [
            'name' => 'Manager',
            'password' => 'manager123',
            'role' => 'manager',
        ]);
    }
}
