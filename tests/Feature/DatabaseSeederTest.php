<?php

use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('seeds demo users customers and orders by default outside production', function () {
    expect(app()->environment('production'))->toBeFalse()
        ->and(config('app.seed_demo_data'))->toBeTrue();

    $this->seed(DatabaseSeeder::class);

    $this->assertDatabaseHas('users', ['username' => 'josh', 'role' => 'employee']);
    $this->assertDatabaseHas('users', ['username' => 'manager', 'role' => 'manager']);
    $this->assertDatabaseCount('customers', 28);
    $this->assertDatabaseCount('laundry_orders', 32);
    $this->assertDatabaseHas('customers', [
        'name' => 'Timeline Customer 20',
        'contact_number' => '09170000020',
    ]);
});

it('skips demo data in production even when explicitly enabled', function () {
    config(['app.seed_demo_data' => true]);
    $this->app['env'] = 'production';

    $this->app->make(DatabaseSeeder::class)->run();

    $this->assertDatabaseCount('services', 6);
    $this->assertDatabaseCount('users', 0);
    $this->assertDatabaseCount('customers', 0);
    $this->assertDatabaseCount('laundry_orders', 0);
});
