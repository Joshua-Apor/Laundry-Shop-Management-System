<?php

namespace App\Services;

use App\Models\Order;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;

class SalesReportService
{
    /**
     * @return array{
     *     period: string,
     *     selected_date: string,
     *     date_label: string,
     *     start_date: string,
     *     end_date: string,
     *     order_count: int,
     *     total_sales: float,
     *     payments_collected: float,
     *     outstanding_balance: float,
     *     average_order: float,
     *     statuses: array<string, array{count: int, total: float}>,
     *     orders: list<array{order_id: int, order_date: string, customer: string, employee: string, status: string, total: float, paid: float, balance: float}>
     * }
     */
    public function generate(string $period, string $date): array
    {
        $selectedDate = CarbonImmutable::createFromFormat('!Y-m-d', $date);

        [$startDate, $endDate] = match ($period) {
            'Daily' => [$selectedDate, $selectedDate],
            'Weekly' => [$selectedDate->startOfWeek(Carbon::MONDAY), $selectedDate->endOfWeek(Carbon::SUNDAY)],
            'Monthly' => [$selectedDate->startOfMonth(), $selectedDate->endOfMonth()],
        };

        $orders = Order::query()
            ->leftJoin('customers', 'customers.customer_id', '=', 'laundry_orders.customer_id')
            ->leftJoin('users', 'users.user_id', '=', 'laundry_orders.user_id')
            ->whereBetween('laundry_orders.order_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->select([
                'laundry_orders.order_id',
                'laundry_orders.order_date',
                'customers.name as customer',
                'users.name as employee',
                'laundry_orders.order_status as status',
                'laundry_orders.total_amount as total',
                'laundry_orders.amount_paid as paid',
                'laundry_orders.balance',
            ])
            ->orderBy('laundry_orders.order_date')
            ->orderBy('laundry_orders.order_id')
            ->get()
            ->map(fn (Order $order): array => [
                'order_id' => (int) $order->order_id,
                'order_date' => (string) $order->order_date,
                'customer' => (string) ($order->customer ?? 'Unknown customer'),
                'employee' => (string) ($order->employee ?? 'Unknown employee'),
                'status' => (string) $order->status,
                'total' => (float) $order->total,
                'paid' => (float) $order->paid,
                'balance' => (float) $order->balance,
            ])
            ->all();

        $totalSales = array_sum(array_column($orders, 'total'));
        $statuses = [];

        foreach ($orders as $order) {
            $statuses[$order['status']] ??= ['count' => 0, 'total' => 0.0];
            $statuses[$order['status']]['count']++;
            $statuses[$order['status']]['total'] += $order['total'];
        }

        $paymentsCollected = (float) DB::table('payments')
            ->whereBetween('payment_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->sum('amount');

        return [
            'period' => $period,
            'selected_date' => $selectedDate->toDateString(),
            'date_label' => $startDate->isSameDay($endDate)
                ? $startDate->format('F j, Y')
                : $startDate->format('M j, Y').' – '.$endDate->format('M j, Y'),
            'start_date' => $startDate->toDateString(),
            'end_date' => $endDate->toDateString(),
            'order_count' => count($orders),
            'total_sales' => $totalSales,
            'payments_collected' => $paymentsCollected,
            'outstanding_balance' => array_sum(array_column($orders, 'balance')),
            'average_order' => count($orders) > 0 ? $totalSales / count($orders) : 0.0,
            'statuses' => $statuses,
            'orders' => $orders,
        ];
    }
}
