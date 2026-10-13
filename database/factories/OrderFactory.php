<?php

namespace Database\Factories;

use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $customerId = DB::table('customers')->inRandomOrder()->value('customer_id');
        $employee = DB::table('users')->where('role', 'employee')->inRandomOrder()->first(['user_id', 'name']);
        $weight = fake()->randomFloat(2, 1, 12);
        $total = round(max($weight, 5) * 35, 2);
        $amountPaid = fake()->boolean(70) ? $total : round($total / 2, 2);
        $status = fake()->randomElement(Order::STATUSES);
        $orderDate = fake()->dateTimeBetween('-90 days', 'now');

        return [
            'customer_id' => $customerId,
            'user_id' => $employee->user_id,
            'employee_name' => $employee->name,
            'order_date' => $orderDate->format('Y-m-d'),
            'order_time' => fake()->time(),
            'laundry_weight' => $weight,
            'order_type' => 'Drop Off',
            'self_service_loads' => null,
            'total_amount' => $total,
            'amount_paid' => $amountPaid,
            'balance' => $total - $amountPaid,
            'order_status' => $status,
            'pickup_date' => $status === 'Completed' ? $orderDate->format('Y-m-d') : null,
            'ready_for_pickup_at' => $status === 'Ready for Pickup' ? now()->subDays(random_int(0, 5)) : null,
            'storage_fee' => 0,
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Order $order): void {
            $service = DB::table('services')->where('service_name', 'Drop Off')->first(['service_id']);

            DB::table('order_services')->insert([
                'order_id' => $order->order_id,
                'service_id' => $service->service_id,
                'quantity' => 1,
                'service_fee' => $order->total_amount,
            ]);

            DB::table('payments')->insert([
                'order_id' => $order->order_id,
                'payment_date' => $order->order_date,
                'amount' => $order->amount_paid,
                'payment_method' => fake()->randomElement(['Cash', 'GCash']),
                'payment_status' => (float) $order->balance === 0.0 ? 'Paid' : 'Partial',
                'reference_number' => 'DEMO-'.$order->order_id,
            ]);
        });
    }
}
