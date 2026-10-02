<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless(in_array($request->user()?->role, ['manager', 'employee'], true), 403);

        $search = $request->string('search')->trim()->toString();

        $customers = Order::query()
            ->leftJoin('customers', 'customers.customer_id', '=', 'laundry_orders.customer_id')
            ->select([
                'customers.customer_id as customer_id',
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
            ->groupBy('customers.customer_id', 'customers.name', 'customers.contact_number', 'customers.address')
            ->orderByDesc('total_spent')
            ->paginate(15)
            ->withQueryString();

        return view('employee.customers.index', [
            'customers' => $customers,
            'search' => $search,
        ]);
    }

    public function update(Request $request, int $customer): RedirectResponse
    {
        abort_unless(in_array($request->user()?->role, ['manager', 'employee'], true), 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'contact_number' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string'],
        ]);

        $updated = DB::table('customers')->where('customer_id', $customer)->update($validated);
        abort_if($updated === 0 && ! DB::table('customers')->where('customer_id', $customer)->exists(), 404);

        return back()->with('success', 'Customer information updated.');
    }

    public function destroy(Request $request, int $customer): RedirectResponse
    {
        abort_unless(in_array($request->user()?->role, ['manager', 'employee'], true), 403);

        $deleted = DB::table('customers')->where('customer_id', $customer)->delete();
        abort_if($deleted === 0, 404);

        return back()->with('success', 'Customer and associated order history removed.');
    }
}
