<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CustomerOrderTimelineSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $employee = User::query()
            ->where('role', 'employee')
            ->orderBy('user_id')
            ->first(['user_id', 'name']);
        $dropOffService = DB::table('services')
            ->where('service_name', 'Drop Off')
            ->whereNull('deleted_at')
            ->first(['service_id', 'base_price']);

        if ($employee === null || $dropOffService === null) {
            throw new \RuntimeException('Seed an employee and the Drop Off service before running the customer timeline seeder.');
        }

        DB::transaction(function () use ($employee, $dropOffService): void {
            for ($customerNumber = 1; $customerNumber <= 20; $customerNumber++) {
                $phoneNumber = sprintf('091700%05d', $customerNumber);
                $customerName = sprintf('Timeline Customer %02d', $customerNumber);

                DB::table('customers')->updateOrInsert(
                    ['contact_number' => $phoneNumber],
                    [
                        'name' => $customerName,
                        'email' => sprintf('timeline.customer%02d@example.test', $customerNumber),
                        'deleted_at' => null,
                    ],
                );

                $customerId = (int) DB::table('customers')
                    ->where('contact_number', $phoneNumber)
                    ->value('customer_id');
                $orderDate = $this->orderDate($customerNumber);
                $weight = 3.5 + ($customerNumber * 0.25);
                $total = round(175 + max((int) floor($weight) - 5, 0) * (float) $dropOffService->base_price, 2);
                $orderStatus = match (true) {
                    $customerNumber <= 5 => 'Processing',
                    $customerNumber <= 8 => 'Ready for Pickup',
                    default => 'Completed',
                };

                DB::table('laundry_orders')->updateOrInsert(
                    [
                        'customer_id' => $customerId,
                        'user_id' => $employee->user_id,
                    ],
                    [
                        'employee_name' => $employee->name,
                        'order_date' => $orderDate,
                        'order_time' => now()->setTime(8 + ($customerNumber % 10), $customerNumber % 60)->format('H:i:s'),
                        'order_type' => 'Drop Off',
                        'laundry_weight' => $weight,
                        'self_service_loads' => null,
                        'total_amount' => $total,
                        'amount_paid' => $total,
                        'balance' => 0,
                        'order_status' => $orderStatus,
                        'pickup_date' => $orderStatus === 'Completed' ? $orderDate : null,
                        'ready_for_pickup_at' => $orderStatus === 'Ready for Pickup' ? $orderDate.' 14:00:00' : null,
                        'storage_fee' => 0,
                    ],
                );

                $orderId = (int) DB::table('laundry_orders')
                    ->where('customer_id', $customerId)
                    ->where('user_id', $employee->user_id)
                    ->value('order_id');

                DB::table('order_services')->updateOrInsert(
                    ['order_id' => $orderId, 'service_id' => $dropOffService->service_id],
                    ['quantity' => 1, 'service_fee' => $total],
                );

                DB::table('payments')->updateOrInsert(
                    ['order_id' => $orderId],
                    [
                        'payment_date' => $orderDate,
                        'amount' => $total,
                        'payment_method' => $customerNumber % 2 === 0 ? 'Cash' : 'GCash',
                        'payment_status' => 'Paid',
                        'reference_number' => sprintf('DEMO-TIMELINE-%02d', $customerNumber),
                    ],
                );
            }
        });
    }

    private function orderDate(int $customerNumber): string
    {
        $today = today();

        return match (true) {
            $customerNumber <= 5 => $today->toDateString(),
            $customerNumber <= 8 => $today->copy()->subDay()->toDateString(),
            $customerNumber <= 12 => $today->copy()->subDays($customerNumber - 6)->toDateString(),
            $customerNumber <= 16 => $today->copy()->subYear()->subDays($customerNumber - 13)->toDateString(),
            default => $today->copy()->subYears(2)->subDays($customerNumber - 17)->toDateString(),
        };
    }
}
