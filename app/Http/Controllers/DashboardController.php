<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $recentOrders = Order::query()
            ->withRecordDetails()
            ->addSelect('laundry_orders.order_id as id')
            ->orderByDesc('laundry_orders.order_date')
            ->orderByDesc('laundry_orders.order_id')
            ->take(4)
            ->get();

        return view('dashboard.index', [
            'totalOrders' => Order::query()->count(),
            'completedOrders' => Order::query()->where('order_status', 'Completed')->count(),
            'readyForPickupOrders' => Order::query()->where('order_status', 'Ready for Pickup')->count(),
            'needNotificationOrders' => Order::query()->where('order_status', 'Ready for Pickup')->count(),
            'totalRevenue' => Order::query()->sum('total_amount'),
            'recentOrders' => $recentOrders,
        ]);
    }
}
