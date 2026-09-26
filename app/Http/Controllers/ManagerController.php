<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ManagerController extends Controller
{
    public function customerRecords(Request $request): View
    {
        $search = $request->string('search')->trim()->toString();
        $customers = Order::query()
            ->leftJoin('customers', 'customers.customer_id', '=', 'laundry_orders.customer_id')
            ->select([
                'customers.name as fullname',
                'customers.contact_number as phoneNumber',
            ])
            ->selectRaw('COUNT(laundry_orders.order_id) as orders_count, SUM(laundry_orders.total_amount) as total_spent')
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('customers.name', 'like', "%{$search}%")
                        ->orWhere('customers.contact_number', 'like', "%{$search}%");
                });
            })
            ->groupBy('customers.name', 'customers.contact_number')
            ->orderByDesc('total_spent')
            ->paginate(15)
            ->withQueryString();

        return view('manager.customer-records', [
            'customers' => $customers,
            'customerCount' => Order::query()->distinct('customer_id')->count('customer_id'),
            'orderCount' => Order::query()->count(),
            'repeatCustomerCount' => Order::query()->select('customer_id')->groupBy('customer_id')->havingRaw('COUNT(*) > 1')->get()->count(),
            'averageSpend' => Order::query()->avg('total_amount') ?? 0,
            'search' => $search,
        ]);
    }

    public function ordersAndPayments(Request $request): View
    {
        $search = $request->string('search')->trim()->toString();
        $orders = Order::query()
            ->leftJoin('customers', 'customers.customer_id', '=', 'laundry_orders.customer_id')
            ->select([
                'laundry_orders.*',
                'laundry_orders.order_id as id',
                'laundry_orders.laundry_weight as weight',
                'laundry_orders.order_status as status',
                'customers.name as fullname',
                'customers.contact_number as phoneNumber',
            ])
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('customers.name', 'like', "%{$search}%")
                        ->orWhere('customers.contact_number', 'like', "%{$search}%")
                        ->orWhere('laundry_orders.order_id', 'like', "%{$search}%");
                });
            })
            ->latest('order_date')
            ->paginate(15)
            ->withQueryString();

        return view('manager.orders-payments', [
            'orders' => $orders,
            'search' => $search,
            'totalRevenue' => Order::query()->sum('total_amount'),
            'totalCollected' => Order::query()->sum('amount_paid'),
            'outstanding' => Order::query()->sum('balance'),
            'completedOrders' => Order::query()->where('order_status', 'Completed')->count(),
        ]);
    }

    public function salesReports(): View
    {
        return view('manager.sales-reports');
    }

    public function employees(): View
    {
        $employees = User::query()
            ->where('role', 'employee')
            ->orderBy('name')
            ->get();

        return view('manager.employees', ['employees' => $employees]);
    }
}
