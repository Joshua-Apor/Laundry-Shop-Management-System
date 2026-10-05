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
        $summary = DB::table('laundry_orders')
            ->selectRaw(
                'COUNT(*) as total_orders,
                COALESCE(SUM(CASE WHEN order_status = ? THEN 1 ELSE 0 END), 0) as completed_orders,
                COALESCE(SUM(CASE WHEN order_status = ? THEN 1 ELSE 0 END), 0) as ready_for_pickup_orders,
                COALESCE(SUM(total_amount), 0) as total_revenue',
                ['Completed', 'Ready for Pickup'],
            )
            ->first();

        $recentOrders = Order::query()
            ->withRecordDetails()
            ->addSelect('laundry_orders.order_id as id')
            ->orderByDesc('laundry_orders.order_date')
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
        ]);
    }
}
