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
        $this->call(ServiceSeeder::class);

        User::query()->updateOrCreate([
            'username' => 'josh',
        ], [
            'name' => 'Demo Employee',
            'email' => 'demo.employee@example.com',
            'password' => 'employee123',
            'role' => 'employee',
        ]);

        User::query()->updateOrCreate([
            'username' => 'manager',
        ], [
            'name' => 'Demo Manager',
            'email' => 'demo.manager@example.com',
            'password' => 'manager123',
            'role' => 'manager',
        ]);

        $employeeId = User::query()->where('username', 'josh')->value('user_id');

        $sampleCustomers = [
            ['name' => 'Maria Santos', 'contact_number' => '09990000001', 'address' => 'Mabini Street'],
            ['name' => 'Daniel Reyes', 'contact_number' => '09990000002', 'address' => 'Rizal Avenue'],
            ['name' => 'Angela Cruz', 'contact_number' => '09990000003', 'address' => 'Bonifacio Road'],
            ['name' => 'Miguel Garcia', 'contact_number' => '09990000004', 'address' => 'Luna Street'],
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

        $services = DB::table('services')
            ->whereIn('service_name', ['Cellophane Blue', 'Cellophane White', 'Drop Off', 'Dry', 'Sabon', 'Self Service'])
            ->get(['service_id', 'service_name', 'base_price'])
            ->keyBy('service_name');

        $sampleOrders = [
            ['customer' => 0, 'days_ago' => 0, 'order_type' => 'Drop Off', 'weight' => 4.50, 'loads' => null, 'services' => ['Drop Off' => 1, 'Cellophane Blue' => 1], 'payment' => 'full', 'status' => 'Processing', 'pickup_days' => null],
            ['customer' => 1, 'days_ago' => 1, 'order_type' => 'Drop Off', 'weight' => 6.25, 'loads' => null, 'services' => ['Drop Off' => 1, 'Cellophane White' => 1], 'payment' => 'partial', 'status' => 'Processing', 'pickup_days' => null],
            ['customer' => 2, 'days_ago' => 2, 'order_type' => 'Self Service', 'weight' => null, 'loads' => 2, 'services' => ['Self Service' => 2, 'Dry' => 2, 'Sabon' => 2], 'payment' => 'full', 'status' => 'Ready for Pickup', 'pickup_days' => 0],
            ['customer' => 3, 'days_ago' => 5, 'order_type' => 'Self Service', 'weight' => null, 'loads' => 1, 'services' => ['Self Service' => 1, 'Dry' => 1, 'Sabon' => 1], 'payment' => 'full', 'status' => 'Completed', 'pickup_days' => -1],
        ];

        foreach ($sampleOrders as $sampleOrder) {
            $orderDate = now()->subDays($sampleOrder['days_ago'])->toDateString();
            $serviceFees = [];

            foreach ($sampleOrder['services'] as $serviceName => $quantity) {
                $service = $services->get($serviceName);
                $serviceFees[$serviceName] = $serviceName === 'Drop Off' && $sampleOrder['weight'] <= 5
                    ? 175
                    : (float) $service->base_price * $quantity;
            }

            $total = array_sum($serviceFees);
            $paid = $sampleOrder['payment'] === 'full' ? $total : round($total / 2, 2);
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
                    'order_type' => $sampleOrder['order_type'],
                    'laundry_weight' => $sampleOrder['weight'],
                    'self_service_loads' => $sampleOrder['loads'],
                    'total_amount' => $total,
                    'amount_paid' => $paid,
                    'balance' => $total - $paid,
                    'order_status' => $sampleOrder['status'],
                    'pickup_date' => $pickupDate,
                ],
            );

            $orderId = DB::table('laundry_orders')
                ->where('customer_id', $customerIds[$sampleOrder['customer']])
                ->where('user_id', $employeeId)
                ->where('order_date', $orderDate)
                ->value('order_id');

            DB::table('order_services')->where('order_id', $orderId)->delete();

            foreach ($sampleOrder['services'] as $serviceName => $quantity) {
                DB::table('order_services')->insert([
                    'order_id' => $orderId,
                    'service_id' => $services->get($serviceName)->service_id,
                    'quantity' => $serviceName === 'Drop Off' ? 1 : $quantity,
                    'service_fee' => $serviceFees[$serviceName],
                ]);
            }

            DB::table('payments')->updateOrInsert(
                ['order_id' => $orderId],
                [
                    'payment_date' => $orderDate,
                    'amount' => $paid,
                    'payment_method' => $sampleOrder['customer'] % 2 === 0 ? 'Cash' : 'GCash',
                    'payment_status' => $paid >= $total ? 'Paid' : 'Partial',
                    'reference_number' => 'DEMO-'.$orderId,
                ],
            );
        }
    }
}
