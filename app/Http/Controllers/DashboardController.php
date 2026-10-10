<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('dashboard.index');
    }

    public function data(): View
    {
        $today = now()->toDateString();
        $summary = DB::table('laundry_orders')
            ->whereDate('order_date', $today)
            ->selectRaw(
                'COUNT(*) as total_orders,
                COALESCE(SUM(CASE WHEN order_status = ? THEN 1 ELSE 0 END), 0) as completed_orders,
                COALESCE(SUM(CASE WHEN order_status = ? THEN 1 ELSE 0 END), 0) as ready_for_pickup_orders,
                COALESCE(SUM(amount_paid), 0) as total_revenue',
                ['Completed', 'Ready for Pickup'],
            )
            ->first();

        $recentOrders = Order::query()
            ->withRecordDetails()
            ->whereDate('laundry_orders.order_date', $today)
            ->where('laundry_orders.order_status', '!=', 'Completed')
            ->addSelect('laundry_orders.order_id as id')
            ->orderByDesc('laundry_orders.order_date')
            ->orderByDesc('laundry_orders.order_id')
            ->take(4)
            ->get();

        $unclaimedOrders = Order::query()
            ->withRecordDetails()
            ->whereDate('laundry_orders.order_date', '<', $today)
            ->where('laundry_orders.order_status', '!=', 'Completed')
            ->addSelect('laundry_orders.order_id as id')
            ->orderByDesc('laundry_orders.order_date')
            ->orderByDesc('laundry_orders.order_time')
            ->orderByDesc('laundry_orders.order_id')
            ->take(4)
            ->get();

        return view('dashboard.data', [
            'totalOrders' => (int) $summary->total_orders,
            'completedOrders' => (int) $summary->completed_orders,
            'readyForPickupOrders' => (int) $summary->ready_for_pickup_orders,
            'needNotificationOrders' => (int) $summary->ready_for_pickup_orders,
            'totalRevenue' => $summary->total_revenue,
            'recentOrders' => $recentOrders,
            'unclaimedOrders' => $unclaimedOrders,
        ]);
    }
}
