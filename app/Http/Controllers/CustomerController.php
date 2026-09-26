<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(): View
    {
        $customers = Order::query()
            ->leftJoin('customers', 'customers.customer_id', '=', 'laundry_orders.customer_id')
            ->select([
                'customers.name as fullname',
                'customers.contact_number as phoneNumber',
            ])
            ->selectRaw('COUNT(laundry_orders.order_id) as orders_count, SUM(laundry_orders.total_amount) as total_spent')
            ->groupBy('customers.name', 'customers.contact_number')
            ->orderByDesc('total_spent')
            ->paginate(15);

        return view('employee.customers.index', ['customers' => $customers]);
    }
}
