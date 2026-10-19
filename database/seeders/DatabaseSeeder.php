<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
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
            'name' => 'Joshua Apor',
            'email' => 'joshuaapor21@gmail.com',
            'password' => '12345678',
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
            ['name' => 'Maria Santos', 'contact_number' => '09990000001', 'email' => 'maria.santos@example.com'],
            ['name' => 'Daniel Reyes', 'contact_number' => '09990000002', 'email' => 'daniel.reyes@example.com'],
            ['name' => 'Angela Cruz', 'contact_number' => '09990000003', 'email' => 'angela.cruz@example.com'],
            ['name' => 'Miguel Garcia', 'contact_number' => '09990000004', 'email' => 'miguel.garcia@example.com'],
            ['name' => 'Sofia Mendoza', 'contact_number' => '09990000005', 'email' => 'sofia.mendoza@example.com'],
            ['name' => 'Gabriel Flores', 'contact_number' => '09990000006', 'email' => 'gabriel.flores@example.com'],
            ['name' => 'Isabella Ramos', 'contact_number' => '09990000007', 'email' => 'isabella.ramos@example.com'],
            ['name' => 'Noah Castillo', 'contact_number' => '09990000008', 'email' => 'noah.castillo@example.com'],
        ];

        $customerIds = [];

        foreach ($sampleCustomers as $customer) {
            DB::table('customers')->updateOrInsert(
                ['contact_number' => $customer['contact_number']],
                ['name' => $customer['name'], 'email' => $customer['email']],
            );

            $customerIds[] = DB::table('customers')
                ->where('contact_number', $customer['contact_number'])
                ->value('customer_id');
        }

        $services = DB::table('services')
            ->whereIn('service_name', ['Cellophane Blue', 'Cellophane White', 'Drop Off', 'Dry', 'Sabon', 'Self Service'])
            ->get(['service_id', 'service_name', 'base_price'])
            ->keyBy('service_name');

        if ($services->count() !== 6) {
            throw new \RuntimeException('All sample order services must exist.');
        }

        $sampleOrders = [
            ['customer' => 0, 'days_ago' => 6, 'weight' => 4.50, 'payment' => 'full', 'status' => 'Completed'],
            ['customer' => 1, 'days_ago' => 5, 'weight' => 6.25, 'payment' => 'full', 'status' => 'Completed'],
            ['customer' => 2, 'days_ago' => 5, 'weight' => 8.00, 'payment' => 'full', 'status' => 'Completed'],
            ['customer' => 3, 'days_ago' => 4, 'weight' => 3.75, 'payment' => 'full', 'status' => 'Completed'],
            ['customer' => 4, 'days_ago' => 4, 'weight' => 5.00, 'payment' => 'full', 'status' => 'Completed'],
            ['customer' => 5, 'days_ago' => 3, 'weight' => 7.50, 'payment' => 'full', 'status' => 'Ready for Pickup'],
            ['customer' => 6, 'days_ago' => 3, 'order_type' => 'Self Service', 'loads' => 2, 'payment' => 'full', 'status' => 'Completed'],
            ['customer' => 7, 'days_ago' => 2, 'weight' => 9.00, 'payment' => 'full', 'status' => 'Completed'],
            ['customer' => 0, 'days_ago' => 2, 'order_type' => 'Self Service', 'loads' => 1, 'payment' => 'full', 'status' => 'Ready for Pickup'],
            ['customer' => 2, 'days_ago' => 1, 'weight' => 4.00, 'payment' => 'full', 'status' => 'Completed'],
            ['customer' => 5, 'days_ago' => 0, 'weight' => 5.50, 'payment' => 'full', 'status' => 'Processing'],
            ['customer' => 7, 'days_ago' => 0, 'order_type' => 'Self Service', 'loads' => 3, 'payment' => 'full', 'status' => 'Processing'],
        ];

        $receiptNumbersByDate = [];

        foreach ($sampleOrders as $sampleOrder) {
            $orderDate = now()->subDays($sampleOrder['days_ago'])->toDateString();
            $orderType = $sampleOrder['order_type'] ?? 'Drop Off';
            $weight = isset($sampleOrder['weight']) ? (float) $sampleOrder['weight'] : null;
            $loads = $sampleOrder['loads'] ?? null;
            if ($orderType === 'Self Service') {
                $serviceFees = [
                    'Self Service' => (float) $services->get('Self Service')->base_price * $loads,
                    'Dry' => (float) $services->get('Dry')->base_price * $loads,
                    'Sabon' => (float) $services->get('Sabon')->base_price * $loads,
                ];
            } else {
                $serviceFees = [
                    'Drop Off' => 175 + max((int) floor($weight) - 5, 0) * (float) $services->get('Drop Off')->base_price,
                ];
                if ($sampleOrder['customer'] % 2 === 0) {
                    $serviceFees['Cellophane Blue'] = (float) $services->get('Cellophane Blue')->base_price;
                }
            }
            $total = round(array_sum($serviceFees), 2);
            $paid = $sampleOrder['payment'] === 'full' ? $total : round($total / 2, 2);
            $pickupDate = $sampleOrder['status'] === 'Completed' ? $orderDate : null;
            $orderTime = Carbon::parse($orderDate)->setTime(9 + ($sampleOrder['customer'] % 8), 15)->format('H:i:s');

            DB::table('laundry_orders')->updateOrInsert(
                [
                    'customer_id' => $customerIds[$sampleOrder['customer']],
                    'user_id' => $employeeId,
                    'order_date' => $orderDate,
                ],
                [
                    'employee_name' => 'Joshua Apor',
                    'order_time' => $orderTime,
                    'order_type' => $orderType,
                    'laundry_weight' => $weight,
                    'self_service_loads' => $loads,
                    'total_amount' => $total,
                    'amount_paid' => $paid,
                    'balance' => $total - $paid,
                    'order_status' => $sampleOrder['status'],
                    'pickup_date' => $pickupDate,
                    'ready_for_pickup_at' => $sampleOrder['status'] === 'Ready for Pickup' ? Carbon::parse($orderDate)->setTime(14, 0) : null,
                    'storage_fee' => 0,
                ],
            );

            $orderId = DB::table('laundry_orders')
                ->where('customer_id', $customerIds[$sampleOrder['customer']])
                ->where('user_id', $employeeId)
                ->where('order_date', $orderDate)
                ->value('order_id');

            DB::table('order_services')->where('order_id', $orderId)->delete();

            foreach ($serviceFees as $serviceName => $serviceFee) {
                DB::table('order_services')->insert([
                    'order_id' => $orderId,
                    'service_id' => $services->get($serviceName)->service_id,
                    'quantity' => in_array($serviceName, ['Self Service', 'Dry', 'Sabon'], true) ? $loads : 1,
                    'service_fee' => $serviceFee,
                ]);
            }

            $existingReference = DB::table('payments')->where('order_id', $orderId)->value('reference_number');
            if (! isset($receiptNumbersByDate[$orderDate])) {
                $receiptNumbersByDate[$orderDate] = (int) DB::table('payment_receipt_sequences')->where('receipt_date', $orderDate)->value('last_number');
            }
            $validExistingReference = is_string($existingReference)
                && preg_match('/^'.Carbon::parse($orderDate)->format('md-y').'\-\d{4}$/', $existingReference) === 1;
            $receiptNumber = $validExistingReference
                ? $existingReference
                : Carbon::parse($orderDate)->format('md-y').'-'.str_pad((string) ++$receiptNumbersByDate[$orderDate], 4, '0', STR_PAD_LEFT);

            DB::table('payments')->updateOrInsert(
                ['order_id' => $orderId],
                [
                    'payment_date' => $orderDate,
                    'amount' => $paid,
                    'payment_method' => $sampleOrder['customer'] % 2 === 0 ? 'Cash' : 'GCash',
                    'payment_status' => $paid >= $total ? 'Paid' : 'Partial',
                    'reference_number' => $receiptNumber,
                ],
            );

            DB::table('claim_receipts')->updateOrInsert(
                ['order_id' => $orderId],
                ['receipt_number' => $receiptNumber, 'issue_date' => $orderDate],
            );

            DB::table('payment_receipt_sequences')->updateOrInsert(
                ['receipt_date' => $orderDate],
                ['last_number' => max((int) DB::table('payment_receipt_sequences')->where('receipt_date', $orderDate)->value('last_number'), $receiptNumbersByDate[$orderDate])],
            );
        }
    }
}
