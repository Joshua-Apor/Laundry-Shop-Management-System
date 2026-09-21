<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $recentOrders = Order::query()
            ->leftJoin('customers', 'customers.customer_id', '=', 'laundry_orders.customer_id')
            ->select([
                'laundry_orders.*',
                'laundry_orders.order_id as id',
                'laundry_orders.laundry_weight as weight',
                'laundry_orders.order_status as status',
                'customers.name as fullname',
                'customers.contact_number as phoneNumber',
            ])
            ->latest('order_date')
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
