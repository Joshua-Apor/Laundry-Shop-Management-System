<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $recentOrders = Order::query()->latest()->take(4)->get();

        return view('dashboard.index', [
            'totalOrders' => Order::query()->count(),
            'completedOrders' => Order::query()->where('status', 'Completed')->count(),
            'readyForPickupOrders' => Order::query()->where('status', 'Ready for Pickup')->count(),
            'needNotificationOrders' => Order::query()->where('status', 'Ready for Pickup')->count(),
            'totalRevenue' => Order::query()->sum('total_amount'),
            'recentOrders' => $recentOrders,
        ]);
    }
}
