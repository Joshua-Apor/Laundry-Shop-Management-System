<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

function createOrderManagementFixture(int $customerId, int $employeeId, string $status, string $date, string $time): int
{
    return (int) DB::table('laundry_orders')->insertGetId([
        'customer_id' => $customerId,
        'user_id' => $employeeId,
        'employee_name' => 'Test Employee',
        'order_date' => $date,
        'order_time' => $time,
        'order_type' => 'Drop Off',
        'laundry_weight' => 5,
        'total_amount' => 175,
        'amount_paid' => 175,
        'balance' => 0,
        'order_status' => $status,
    ]);
}

function createOrderManagementCustomer(): int
{
    return (int) DB::table('customers')->insertGetId([
        'name' => 'Test Customer',
        'contact_number' => '09123456789',
        'email' => null,
    ]);
}

it('separates finalized employee orders and sorts each view newest first', function () {
    $user = User::factory()->create(['role' => 'employee']);
    $customerId = createOrderManagementCustomer();
    $oldCompletedId = createOrderManagementFixture($customerId, (int) $user->getKey(), 'Completed', '2026-10-11', '09:00:00');
    $newCancelledId = createOrderManagementFixture($customerId, (int) $user->getKey(), 'Cancelled', '2026-10-12', '11:00:00');
    $olderActiveId = createOrderManagementFixture($customerId, (int) $user->getKey(), 'Processing', '2026-10-13', '09:00:00');
    $newerActiveId = createOrderManagementFixture($customerId, (int) $user->getKey(), 'Ready for Pickup', '2026-10-13', '10:00:00');

    $this->actingAs($user)
        ->get(route('records.index', ['view' => 'history']))
        ->assertViewHas('orders', function ($orders) use ($newCancelledId, $oldCompletedId): bool {
            return $orders->pluck('order_id')->all() === [$newCancelledId, $oldCompletedId];
        });

    $this->actingAs($user)
        ->get(route('records.index', ['view' => 'active']))
        ->assertViewHas('orders', fn ($orders): bool => $orders->pluck('order_id')->all() === [$newerActiveId, $olderActiveId]);
});

it('separates finalized manager orders from active orders', function () {
    $user = User::factory()->create(['role' => 'manager']);
    $customerId = createOrderManagementCustomer();
    $activeOrderId = createOrderManagementFixture($customerId, (int) $user->getKey(), 'Processing', '2026-10-13', '10:00:00');
    $historyOrderId = createOrderManagementFixture($customerId, (int) $user->getKey(), 'Cancelled', '2026-10-12', '11:00:00');

    $this->actingAs($user)
        ->get(route('manager.orders-payments', ['view' => 'history']))
        ->assertViewHas('orders', fn ($orders): bool => $orders->pluck('order_id')->all() === [$historyOrderId]);

    $this->actingAs($user)
        ->get(route('manager.orders-payments', ['view' => 'active']))
        ->assertViewHas('orders', fn ($orders): bool => $orders->pluck('order_id')->all() === [$activeOrderId]);
});

it('returns a customer order history in descending order with the order details', function () {
    $user = User::factory()->create(['role' => 'manager']);
    $customerId = createOrderManagementCustomer();
    $serviceId = DB::table('services')->insertGetId([
        'service_name' => 'Drop Off',
        'description' => 'Standard service',
        'base_price' => 175,
    ]);
    $olderOrderId = createOrderManagementFixture($customerId, (int) $user->getKey(), 'Completed', '2026-10-11', '09:00:00');
    $newerOrderId = createOrderManagementFixture($customerId, (int) $user->getKey(), 'Cancelled', '2026-10-12', '11:00:00');
    DB::table('order_services')->insert([
        'order_id' => $newerOrderId,
        'service_id' => $serviceId,
        'quantity' => 1,
        'service_fee' => 175,
    ]);

    $response = $this->actingAs($user)->get(route('customers.orders', $customerId));

    $response->assertOk()
        ->assertSee("Order #{$newerOrderId}")
        ->assertSee('Cancelled')
        ->assertSee('Drop Off')
        ->assertSee('View Details')
        ->assertSee('border-rose-200 bg-rose-50 text-rose-700')
        ->assertSee('data-history-detail-modal="customer-history-'.$customerId.'"', false)
        ->assertSee('data-modal-back-target="customer-history-'.$customerId.'"', false)
        ->assertSee('175.00');
    expect($response->getContent())->toContain("Order #{$newerOrderId}")
        ->and(strpos($response->getContent(), "Order #{$newerOrderId}"))->toBeLessThan(strpos($response->getContent(), "Order #{$olderOrderId}"));
});
