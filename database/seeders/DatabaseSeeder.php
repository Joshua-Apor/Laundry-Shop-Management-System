<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

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

        $employeeId = User::query()->where('username', 'josh')->value('user_id');

        $sampleCustomers = [
            ['name' => 'Demo Customer One', 'contact_number' => '09990000001', 'address' => 'Demo Address 1'],
            ['name' => 'Demo Customer Two', 'contact_number' => '09990000002', 'address' => 'Demo Address 2'],
            ['name' => 'Demo Customer Three', 'contact_number' => '09990000003', 'address' => 'Demo Address 3'],
            ['name' => 'Demo Customer Four', 'contact_number' => '09990000004', 'address' => 'Demo Address 4'],
        ];

        $customerIds = [];

        foreach ($sampleCustomers as $customer) {
            DB::table('customers')->updateOrInsert(
                ['contact_number' => $customer['contact_number']],
                ['name' => $customer['name'], 'address' => $customer['address']],
            );

            $customerIds[] = DB::table('customers')
                ->where('contact_number', $customer['contact_number'])
                ->value('customer_id');
        }

        $sampleOrders = [
            ['customer' => 0, 'days_ago' => 0, 'weight' => 4.50, 'total' => 270.00, 'paid' => 270.00, 'status' => 'Received', 'pickup_days' => null],
            ['customer' => 1, 'days_ago' => 1, 'weight' => 6.25, 'total' => 375.00, 'paid' => 200.00, 'status' => 'Processing', 'pickup_days' => null],
            ['customer' => 2, 'days_ago' => 2, 'weight' => 3.00, 'total' => 180.00, 'paid' => 180.00, 'status' => 'Ready for Pickup', 'pickup_days' => 0],
            ['customer' => 3, 'days_ago' => 5, 'weight' => 5.00, 'total' => 300.00, 'paid' => 300.00, 'status' => 'Completed', 'pickup_days' => -1],
        ];

        foreach ($sampleOrders as $sampleOrder) {
            $orderDate = now()->subDays($sampleOrder['days_ago'])->toDateString();
            $pickupDate = $sampleOrder['pickup_days'] === null
                ? null
                : now()->addDays($sampleOrder['pickup_days'])->toDateString();

            DB::table('laundry_orders')->updateOrInsert(
                [
                    'customer_id' => $customerIds[$sampleOrder['customer']],
                    'user_id' => $employeeId,
                    'order_date' => $orderDate,
                ],
                [
                    'laundry_weight' => $sampleOrder['weight'],
                    'total_amount' => $sampleOrder['total'],
                    'amount_paid' => $sampleOrder['paid'],
                    'balance' => $sampleOrder['total'] - $sampleOrder['paid'],
                    'order_status' => $sampleOrder['status'],
                    'pickup_date' => $pickupDate,
                ],
            );

            $orderId = DB::table('laundry_orders')
                ->where('customer_id', $customerIds[$sampleOrder['customer']])
                ->where('user_id', $employeeId)
                ->where('order_date', $orderDate)
                ->value('order_id');

            DB::table('payments')->updateOrInsert(
                ['order_id' => $orderId],
                [
                    'payment_date' => $orderDate,
                    'amount' => $sampleOrder['paid'],
                    'payment_method' => $sampleOrder['customer'] % 2 === 0 ? 'Cash' : 'GCash',
                    'payment_status' => $sampleOrder['paid'] >= $sampleOrder['total'] ? 'Paid' : 'Partial',
                    'reference_number' => 'DEMO-'.$orderId,
                ],
            );
        }
    }
}
