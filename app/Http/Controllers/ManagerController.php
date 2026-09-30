<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
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
                'customers.address as address',
            ])
            ->selectRaw('COUNT(laundry_orders.order_id) as orders_count, SUM(laundry_orders.total_amount) as total_spent, MIN(laundry_orders.order_date) as first_order_date')
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('customers.name', 'like', "%{$search}%")
                        ->orWhere('customers.contact_number', 'like', "%{$search}%");
                });
            })
            ->groupBy('customers.name', 'customers.contact_number', 'customers.address')
            ->orderByDesc('total_spent')
            ->paginate(15)
            ->withQueryString();

        return view('manager.customer-records', [
            'customers' => $customers,
            'search' => $search,
        ]);
    }

    public function ordersAndPayments(Request $request): View
    {
        $search = $request->string('search')->trim()->toString();

        $orders = Order::query()
            ->withRecordDetails()
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('customers.name', 'like', "%{$search}%")
                        ->orWhere('customers.contact_number', 'like', "%{$search}%")
                        ->orWhere('laundry_orders.order_id', 'like', "%{$search}%");
                });
            })
            ->latest('laundry_orders.order_date')
            ->paginate(15)
            ->withQueryString();

        return view('manager.orders-payments', [
            'orders' => $orders,
            'search' => $search,
        ]);
    }

    public function salesReports(): View
    {
        return view('manager.sales-reports');
    }

    public function employees(Request $request): View
    {
        abort_unless($request->user()?->role === 'manager', 403);

        $employees = User::query()
            ->where('role', 'employee')
            ->orderBy('name')
            ->get();

        return view('manager.employees', ['employees' => $employees]);
    }

    public function storeEmployee(Request $request): RedirectResponse
    {
        abort_unless($request->user()?->role === 'manager', 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users,username'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'password.min' => 'The password must be at least 8 characters long.',
        ]);

        User::query()->create([
            'name' => $validated['name'],
            'username' => $validated['username'],
            'password' => $validated['password'],
            'role' => 'employee',
        ]);

        return redirect()->route('manager.employees')->with('status', 'Employee account created.');
    }
}
