<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(): View
    {
        $customers = Order::query()
            ->select(['fullname', 'phoneNumber'])
            ->selectRaw('COUNT(*) as orders_count, SUM(total_amount) as total_spent')
            ->groupBy('fullname', 'phoneNumber')
            ->orderByDesc('total_spent')
            ->paginate(15);

        return view('customers.index', ['customers' => $customers]);
    }
}
