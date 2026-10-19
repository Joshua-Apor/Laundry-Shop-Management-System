<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

uses(RefreshDatabase::class);

it('adds a restrictive employee foreign key even when the prior schema has none', function () {
    $employeeForeignKey = collect(Schema::getForeignKeys('laundry_orders'))
        ->first(fn (array $foreignKey): bool => $foreignKey['columns'] === ['user_id']);

    expect($employeeForeignKey)->not->toBeNull()
        ->and(strtolower((string) $employeeForeignKey['on_delete']))->toBe('restrict');
});

it('preserves an employee and order history when permanent deletion is requested', function () {
    $manager = User::factory()->create(['role' => 'manager']);
    $employee = User::factory()->create(['role' => 'employee']);
    $customerId = DB::table('customers')->insertGetId([
        'name' => 'Order History Customer',
        'contact_number' => '09123456789',
        'email' => null,
    ]);
    $orderId = DB::table('laundry_orders')->insertGetId([
        'customer_id' => $customerId,
        'user_id' => $employee->getKey(),
        'employee_name' => $employee->name,
        'order_date' => '2026-10-20',
        'order_time' => '09:00:00',
        'order_type' => 'Drop Off',
        'total_amount' => 175,
        'amount_paid' => 175,
        'balance' => 0,
        'order_status' => 'Completed',
    ]);
    DB::table('payments')->insert([
        'order_id' => $orderId,
        'payment_date' => '2026-10-20',
        'amount' => 175,
        'payment_method' => 'Cash',
        'payment_status' => 'Paid',
        'reference_number' => '1020-26-0001',
    ]);

    $this->actingAs($manager)
        ->delete(route('manager.employees.destroy', $employee), [
            'confirmation_name' => $employee->name,
        ])
        ->assertRedirect(route('manager.employees'))
        ->assertSessionHasErrors('employee');

    $this->assertDatabaseHas('users', ['user_id' => $employee->getKey(), 'deleted_at' => null]);
    $this->assertDatabaseHas('laundry_orders', ['order_id' => $orderId, 'user_id' => $employee->getKey()]);
    $this->assertDatabaseHas('payments', ['order_id' => $orderId, 'amount' => 175]);
});
