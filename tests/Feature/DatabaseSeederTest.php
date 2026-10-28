<?php

use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('seeds services and the demo manager without demo employees customers or orders', function () {
    expect(app()->environment('production'))->toBeFalse()
        ->and(config('app.seed_demo_data'))->toBeTrue();

    $this->seed(DatabaseSeeder::class);

    $this->assertDatabaseHas('users', ['username' => 'manager', 'role' => 'manager']);
    $this->assertDatabaseMissing('users', ['role' => 'employee']);
    $this->assertDatabaseCount('customers', 0);
    $this->assertDatabaseCount('laundry_orders', 0);
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
