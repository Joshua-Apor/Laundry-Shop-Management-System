<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('dashboard.index');
    }

    public function data(): View
    {
        try {
            return $this->dashboardData();
        } catch (Throwable $exception) {
            Log::error('Failed to load employee dashboard data.', [
                'exception' => $exception,
            ]);

            abort(503, 'Dashboard data is temporarily unavailable.');
        }
    }

    private function dashboardData(): View
    {
        $today = now()->toDateString();
        $yesterday = now()->subDay()->toDateString();
        $trendStart = now()->subDays(6)->toDateString();
        $summary = DB::table('laundry_orders')
            ->whereBetween('order_date', [$yesterday, $today])
            ->selectRaw(
                'COALESCE(SUM(CASE WHEN order_date = ? THEN 1 ELSE 0 END), 0) as total_orders,
                COALESCE(SUM(CASE WHEN order_date = ? AND order_status = ? THEN 1 ELSE 0 END), 0) as completed_orders,
                COALESCE(SUM(CASE WHEN order_date = ? AND order_status = ? THEN 1 ELSE 0 END), 0) as ready_for_pickup_orders,
                COALESCE(SUM(CASE WHEN order_date = ? THEN amount_paid ELSE 0 END), 0) as total_revenue,
                COALESCE(SUM(CASE WHEN order_date = ? THEN total_amount ELSE 0 END), 0) as total_order_amount,
                COALESCE(SUM(CASE WHEN order_date = ? THEN total_amount ELSE 0 END), 0) as yesterday_total_order_amount,
                COALESCE(SUM(CASE WHEN order_date = ? THEN amount_paid ELSE 0 END), 0) as yesterday_total_revenue,
                COALESCE(SUM(CASE WHEN order_date = ? THEN 1 ELSE 0 END), 0) as yesterday_total_orders,
                COALESCE(SUM(CASE WHEN order_date = ? AND order_status = ? THEN 1 ELSE 0 END), 0) as yesterday_completed_orders,
                COALESCE(SUM(CASE WHEN order_date = ? AND order_status = ? THEN 1 ELSE 0 END), 0) as yesterday_ready_for_pickup_orders',
                [
                    $today,
                    $today, 'Completed',
                    $today, 'Ready for Pickup',
                    $today,
                    $today,
                    $yesterday,
                    $yesterday,
                    $yesterday,
                    $yesterday, 'Completed',
                    $yesterday, 'Ready for Pickup',
                ],
            )
            ->first();

        $dailyRevenue = DB::table('laundry_orders')
            ->whereBetween('order_date', [$trendStart, $today])
            ->select('order_date')
            ->selectRaw('SUM(amount_paid) as revenue')
            ->groupBy('order_date')
            ->orderBy('order_date')
            ->pluck('revenue', 'order_date');

        $revenueTrend = collect(range(6, 0))
            ->map(function (int $daysAgo) use ($dailyRevenue): array {
                $date = now()->subDays($daysAgo)->toDateString();

                return [
                    'date' => $date,
                    'revenue' => (float) $dailyRevenue->get($date, 0),
                ];
            })
            ->values();
        $maxRevenue = max(1, (float) $revenueTrend->max('revenue'));
        $revenueTrendPoints = $revenueTrend
            ->map(function (array $day, int $index) use ($maxRevenue): string {
                $x = 2 + ($index * 16);
                $y = 36 - (($day['revenue'] / $maxRevenue) * 32);

                return number_format($x, 1, '.', '').','.number_format($y, 1, '.', '');
            })
            ->implode(' ');

        $formatComparison = static function (float|int $current, float|int $previous): array {
            if ((float) $previous === 0.0) {
                return [
                    'value' => (float) $current === 0.0 ? '0%' : 'New',
                    'direction' => (float) $current === 0.0 ? 'flat' : 'up',
                ];
            }

            $change = (($current - $previous) / $previous) * 100;

            return [
                'value' => sprintf('%+.1f%%', $change),
                'direction' => $change > 0 ? 'up' : ($change < 0 ? 'down' : 'flat'),
            ];
        };

        $recentOrders = Order::query()
            ->withRecordDetails()
            ->whereDate('laundry_orders.order_date', $today)
            ->where('laundry_orders.order_status', '!=', 'Completed')
            ->addSelect('laundry_orders.order_id as id')
            ->orderBy('laundry_orders.order_date')
            ->orderBy('laundry_orders.order_time')
            ->orderBy('laundry_orders.order_id')
            ->take(4)
            ->get();

        $unclaimedOrders = Order::query()
            ->withRecordDetails()
            ->whereDate('laundry_orders.order_date', '<', $today)
            ->where('laundry_orders.order_status', '!=', 'Completed')
            ->addSelect('laundry_orders.order_id as id')
            ->orderBy('laundry_orders.order_date')
            ->orderBy('laundry_orders.order_time')
            ->orderBy('laundry_orders.order_id')
            ->take(4)
            ->get();

        return view('dashboard.data', [
            'totalOrders' => (int) $summary->total_orders,
            'averageOrderValue' => (int) $summary->total_orders > 0
                ? (float) $summary->total_order_amount / (int) $summary->total_orders
                : 0,
            'averageOrderValueComparison' => $formatComparison(
                (int) $summary->total_orders > 0
                    ? (float) $summary->total_order_amount / (int) $summary->total_orders
                    : 0,
                (int) $summary->yesterday_total_orders > 0
                    ? (float) $summary->yesterday_total_order_amount / (int) $summary->yesterday_total_orders
                    : 0,
            ),
            'needNotificationOrders' => (int) $summary->ready_for_pickup_orders,
            'totalRevenue' => $summary->total_revenue,
            'revenueTrendPoints' => $revenueTrendPoints,
            'totalRevenueComparison' => $formatComparison((float) $summary->total_revenue, (float) $summary->yesterday_total_revenue),
            'totalOrdersComparison' => $formatComparison((int) $summary->total_orders, (int) $summary->yesterday_total_orders),
            'completedOrdersComparison' => $formatComparison((int) $summary->completed_orders, (int) $summary->yesterday_completed_orders),
            'readyForPickupOrdersComparison' => $formatComparison((int) $summary->ready_for_pickup_orders, (int) $summary->yesterday_ready_for_pickup_orders),
            'recentOrders' => $recentOrders,
            'unclaimedOrders' => $unclaimedOrders,
        ]);
    }
}
