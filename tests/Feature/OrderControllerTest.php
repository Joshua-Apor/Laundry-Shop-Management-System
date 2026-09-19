<?php

use App\Models\Order;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

it('creates an order and redirects to the order index', function () {
    $response = $this->post(route('orders.store'), [
        'full_name' => 'Maria Santos',
        'phone_number' => '09171234567',
        'services' => ['Wash & Dry', 'Ironing'],
        'weight' => 5,
        'special_request' => 'Separate whites',
        'payment_method' => 'GCash',
        'amount_paid' => 100,
    ]);

    $response->assertRedirectToRoute('orders.index');
    $this->assertDatabaseHas('orders', [
        'fullname' => 'Maria Santos',
        'phoneNumber' => '09171234567',
        'paymentMethod' => 'GCash',
        'total_amount' => 330,
        'amount_paid' => 100,
        'status' => 'Received',
    ]);
});

it('shows stored orders on the order index', function () {
    Order::query()->create([
        'fullname' => 'Maria Santos',
        'phoneNumber' => '09171234567',
        'service' => ['Wash & Dry'],
        'weight' => 3,
        'paymentMethod' => 'Cash',
        'amount_paid' => 0,
        'total_amount' => 180,
        'status' => 'Received',
    ]);

    $this->get(route('orders.index'))
        ->assertSee('Maria Santos')
        ->assertSee('Wash &amp; Dry', false)
        ->assertSee('₱180.00');
});

it('shows recent orders and totals on the dashboard', function () {
    Order::query()->create([
        'fullname' => 'Maria Santos',
        'phoneNumber' => '09171234567',
        'service' => ['Wash & Dry'],
        'weight' => 3,
        'paymentMethod' => 'Cash',
        'amount_paid' => 0,
        'total_amount' => 180,
        'status' => 'Received',
    ]);

    $this->get(route('dashboard'))
        ->assertSee('Maria Santos')
        ->assertSee('₱180.00');
});

it('shows customers grouped from their orders', function () {
    Order::query()->create([
        'fullname' => 'Maria Santos',
        'phoneNumber' => '09171234567',
        'service' => ['Wash & Dry'],
        'weight' => 3,
        'paymentMethod' => 'Cash',
        'amount_paid' => 0,
        'total_amount' => 180,
        'status' => 'Received',
    ]);

    $this->get(route('customers.index'))
        ->assertSee('Maria Santos')
        ->assertSee('09171234567')
        ->assertSee('₱180.00');
});

it('requires the order details', function () {
    $response = $this->from(route('orders.create'))->post(route('orders.store'), []);

    $response->assertRedirectBackWithErrors([
        'full_name',
        'phone_number',
        'services',
        'weight',
        'payment_method',
    ]);
    $this->assertDatabaseCount('orders', 0);
});

it('filters orders by status and search term', function () {
    Order::query()->create([
        'fullname' => 'Maria Santos',
        'phoneNumber' => '09171234567',
        'service' => ['Wash & Dry'],
        'weight' => 3,
        'paymentMethod' => 'Cash',
        'total_amount' => 180,
        'status' => 'Processing',
    ]);
    Order::query()->create([
        'fullname' => 'John Cruz',
        'phoneNumber' => '09981234567',
        'service' => ['Folding'],
        'weight' => 2,
        'paymentMethod' => 'Cash',
        'total_amount' => 120,
        'status' => 'Received',
    ]);

    $this->get(route('orders.index', ['status' => 'Processing', 'search' => 'Maria']))
        ->assertSee('Maria Santos')
        ->assertDontSee('John Cruz');
});

it('updates an order status', function () {
    $order = Order::query()->create([
        'fullname' => 'Maria Santos',
        'phoneNumber' => '09171234567',
        'service' => ['Wash & Dry'],
        'weight' => 3,
        'paymentMethod' => 'Cash',
        'total_amount' => 180,
        'status' => 'Received',
    ]);

    $this->patch(route('orders.status.update', $order), ['status' => 'Ready for Pickup'])
        ->assertRedirectToRoute('orders.index');

    $this->assertDatabaseHas('orders', ['id' => $order->id, 'status' => 'Ready for Pickup']);
});

it('records the remaining payment for an order', function () {
    $order = Order::query()->create([
        'fullname' => 'Maria Santos',
        'phoneNumber' => '09171234567',
        'service' => ['Wash & Dry'],
        'weight' => 3,
        'paymentMethod' => 'Cash',
        'amount_paid' => 20,
        'total_amount' => 180,
        'status' => 'Received',
    ]);

    $this->patch(route('orders.payment.record', $order))
        ->assertRedirectToRoute('orders.index');

    $this->assertDatabaseHas('orders', ['id' => $order->id, 'amount_paid' => 180]);
});
