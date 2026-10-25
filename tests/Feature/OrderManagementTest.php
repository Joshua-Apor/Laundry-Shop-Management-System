<?php

use App\Models\User;
use App\Services\SalesReportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
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
        ->get(route('records.index', ['view' => 'history', 'date_preset' => 'all']))
        ->assertViewHas('orders', function ($orders) use ($newCancelledId, $oldCompletedId): bool {
            return $orders->pluck('order_id')->all() === [$newCancelledId, $oldCompletedId];
        });

    $this->actingAs($user)
        ->get(route('records.index', ['view' => 'active', 'date_preset' => 'all']))
        ->assertViewHas('orders', fn ($orders): bool => $orders->pluck('order_id')->all() === [$newerActiveId, $olderActiveId]);
});

it('separates finalized manager orders from active orders', function () {
    $user = User::factory()->create(['role' => 'manager']);
    $customerId = createOrderManagementCustomer();
    $activeOrderId = createOrderManagementFixture($customerId, (int) $user->getKey(), 'Processing', '2026-10-13', '10:00:00');
    $historyOrderId = createOrderManagementFixture($customerId, (int) $user->getKey(), 'Cancelled', '2026-10-12', '11:00:00');

    $this->actingAs($user)
        ->get(route('manager.orders-payments', ['view' => 'history', 'date_preset' => 'all']))
        ->assertViewHas('orders', fn ($orders): bool => $orders->pluck('order_id')->all() === [$historyOrderId]);

    $this->actingAs($user)
        ->get(route('manager.orders-payments', ['view' => 'active', 'date_preset' => 'all']))
        ->assertViewHas('orders', fn ($orders): bool => $orders->pluck('order_id')->all() === [$activeOrderId]);
});

it('soft deletes a customer while retaining the order in sales reports', function () {
    $manager = User::factory()->create(['role' => 'manager']);
    $customerId = createOrderManagementCustomer();
    $orderId = createOrderManagementFixture($customerId, (int) $manager->getKey(), 'Completed', '2026-10-13', '10:00:00');

    $this->actingAs($manager)
        ->delete(route('customers.destroy', $customerId), ['confirmation_name' => 'Test Customer'])
        ->assertRedirect();

    expect(DB::table('customers')->where('customer_id', $customerId)->value('deleted_at'))->not->toBeNull()
        ->and(DB::table('laundry_orders')->where('order_id', $orderId)->exists())->toBeTrue();

    $report = app(SalesReportService::class)->generate('Daily', '2026-10-13');

    expect($report['order_count'])->toBe(1)
        ->and($report['total_sales'])->toBe(175.0)
        ->and($report['orders'][0]['customer'])->toBe('Test Customer');
});

it('filters customer records by orders placed today', function () {
    Carbon::setTestNow('2026-10-13 12:00:00');
    $employee = User::factory()->create(['role' => 'employee']);
    $todayCustomerId = createOrderManagementCustomer();
    $olderCustomerId = (int) DB::table('customers')->insertGetId([
        'name' => 'Older Customer',
        'contact_number' => '09987654321',
        'email' => null,
    ]);
    createOrderManagementFixture($todayCustomerId, (int) $employee->getKey(), 'Completed', '2026-10-13', '10:00:00');
    createOrderManagementFixture($olderCustomerId, (int) $employee->getKey(), 'Completed', '2026-10-12', '10:00:00');

    $response = $this->actingAs($employee)->get(route('customers.index', ['activity' => 'today']));

    $response->assertOk()
        ->assertSee('Today · October 13, 2026')
        ->assertViewHas('customers', function ($customers) use ($todayCustomerId): bool {
            return $customers->count() === 1 && (int) $customers->first()->customer_id === $todayCustomerId;
        });
    Carbon::setTestNow();
});

it('defaults active employee orders to today while keeping all-time history available', function () {
    Carbon::setTestNow('2026-10-13 12:00:00');
    $employee = User::factory()->create(['role' => 'employee']);
    $customerId = createOrderManagementCustomer();
    $todayOrderId = createOrderManagementFixture($customerId, (int) $employee->getKey(), 'Processing', '2026-10-13', '10:00:00');
    createOrderManagementFixture($customerId, (int) $employee->getKey(), 'Processing', '2026-10-12', '10:00:00');

    $this->actingAs($employee)
        ->get(route('records.index'))
        ->assertOk()
        ->assertViewHas('orders', fn ($orders): bool => $orders->pluck('order_id')->all() === [$todayOrderId]);

    $this->actingAs($employee)
        ->get(route('records.index', ['date_preset' => 'all']))
        ->assertViewHas('orders', fn ($orders): bool => $orders->count() === 2);

    Carbon::setTestNow();
});

it('groups orders by date and renders numbered pagination links', function () {
    Carbon::setTestNow('2026-10-25 12:00:00');
    $manager = User::factory()->create(['role' => 'manager']);
    $customerId = createOrderManagementCustomer();
    createOrderManagementFixture($customerId, (int) $manager->getKey(), 'Processing', '2026-10-25', '10:00:00');
    createOrderManagementFixture($customerId, (int) $manager->getKey(), 'Processing', '2026-10-24', '10:00:00');
    createOrderManagementFixture($customerId, (int) $manager->getKey(), 'Processing', '2025-10-24', '10:00:00');

    $timelineResponse = $this->actingAs($manager)->get(route('manager.orders-payments', ['date_preset' => 'all']));
    $timelineResponse->assertOk()
        ->assertSee('Today · October 25, 2026')
        ->assertSee('Yesterday · October 24, 2026')
        ->assertSee('2025');

    for ($index = 0; $index < 15; $index++) {
        createOrderManagementFixture($customerId, (int) $manager->getKey(), 'Processing', '2026-10-23', sprintf('%02d:00:00', $index));
    }

    $this->actingAs($manager)
        ->get(route('manager.orders-payments', ['date_preset' => 'all']))
        ->assertSee('aria-label="Go to page 2"', false)
        ->assertSee('aria-current="page"', false);

    Carbon::setTestNow();
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

it('renders the order detail page for an existing order', function () {
    $user = User::factory()->create(['role' => 'employee']);
    $customerId = createOrderManagementCustomer();
    $orderId = createOrderManagementFixture($customerId, (int) $user->getKey(), 'Processing', '2026-10-20', '10:00:00');

    $this->actingAs($user)
        ->get(route('records.show', $orderId))
        ->assertOk()
        ->assertSee("Order #{$orderId}")
        ->assertSee('Test Customer')
        ->assertSee('PHP 175.00');
});
