<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

uses(RefreshDatabase::class);

it('does not allow a manager to create an order through the employee endpoint', function () {
    $manager = User::factory()->create(['role' => 'manager']);

    $this->actingAs($manager)
        ->post(route('orders.store'), [])
        ->assertForbidden();

    $this->assertDatabaseCount('laundry_orders', 0);
});

it('renders a comma-ready price display with a separate numeric submission field', function () {
    $employee = User::factory()->create(['role' => 'employee']);

    $this->actingAs($employee)
        ->get(route('orders.create'))
        ->assertOk()
        ->assertSee('data-special-request-price-display', false)
        ->assertSee('name="special_request_price"', false)
        ->assertSee('maxlength="8"', false);
});

it('allows an employee to create an order with the maximum additional price', function () {
    Mail::fake();

    $employee = User::factory()->create(['role' => 'employee']);
    $serviceId = DB::table('services')->insertGetId([
        'service_name' => 'Drop Off',
        'description' => 'Standard laundry service',
        'base_price' => 35,
        'price_unit' => '/kg',
    ]);

    $this->actingAs($employee)
        ->post(route('orders.store'), [
            'full_name' => 'Test Customer',
            'phone_number' => '09123456789',
            'order_type' => 'Drop Off',
            'weight' => 5,
            'services' => [$serviceId],
            'special_request' => 'Handle with care',
            'special_request_price' => '9999.00',
            'payment_method' => 'Cash',
            'amount_paid' => '0.01',
        ])
        ->assertRedirect(route('records.index'))
        ->assertSessionHas('success', 'Order created successfully.');

    $this->assertDatabaseHas('laundry_orders', [
        'user_id' => $employee->getKey(),
        'total_amount' => 10174,
    ]);
    $this->assertDatabaseHas('special_requests', [
        'additional_fee' => 9999,
    ]);
});

it('rejects additional prices greater than 9999 before creating customer or order records', function () {
    $employee = User::factory()->create(['role' => 'employee']);
    $serviceId = DB::table('services')->insertGetId([
        'service_name' => 'Drop Off',
        'description' => 'Standard laundry service',
        'base_price' => 35,
        'price_unit' => '/kg',
    ]);

    $this->actingAs($employee)
        ->from(route('orders.create'))
        ->post(route('orders.store'), [
            'full_name' => 'Test Customer',
            'phone_number' => '09123456789',
            'order_type' => 'Drop Off',
            'weight' => 5,
            'services' => [$serviceId],
            'special_request_price' => '9999.01',
            'payment_method' => 'Cash',
            'amount_paid' => '0.01',
        ])
        ->assertRedirect(route('orders.create'))
        ->assertSessionHasErrors('special_request_price');

    $this->assertDatabaseCount('customers', 0);
    $this->assertDatabaseCount('laundry_orders', 0);
});
