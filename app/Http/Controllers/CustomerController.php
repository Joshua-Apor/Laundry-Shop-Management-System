<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless(in_array($request->user()?->role, ['manager', 'employee'], true), 403);

        $search = $request->string('search')->trim()->toString();
        $sort = $request->string('sort')->toString();
        $sort = in_array($sort, ['alphabetical', 'newest', 'oldest'], true) ? $sort : 'newest';

        [$sortColumn, $sortDirection] = match ($sort) {
            'alphabetical' => ['customers.name', 'asc'],
            'oldest' => ['first_order_date', 'asc'],
            default => ['first_order_date', 'desc'],
        };

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
            ->orderBy($sortColumn, $sortDirection)
            ->orderBy('customers.name')
            ->paginate(15)
            ->withQueryString();

        return view('employee.customers.index', [
            'customers' => $customers,
            'search' => $search,
            'sort' => $sort,
        ]);
    }

    public function history(Request $request, int $customer): View
    {
        abort_unless(in_array($request->user()?->role, ['manager', 'employee'], true), 403);
        abort_unless(DB::table('customers')->where('customer_id', $customer)->exists(), 404);

        $orders = Order::query()
            ->withRecordDetails()
            ->where('laundry_orders.customer_id', $customer)
            ->orderByDesc('laundry_orders.order_date')
            ->orderByDesc('laundry_orders.order_time')
            ->orderByDesc('laundry_orders.order_id')
            ->get();

        return view('components.customers.order-history', [
            'orders' => $orders,
            'backModalId' => 'customer-history-'.$customer,
        ]);
    }

    public function update(Request $request, int $customer): RedirectResponse
    {
        abort_unless(in_array($request->user()?->role, ['manager', 'employee'], true), 403);

        $request->merge([
            'contact_number' => preg_replace('/\\D+/', '', (string) $request->input('contact_number', '')),
        ]);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'contact_number' => ['required', 'string', 'regex:/^09\\d{9}$/'],
            'address' => ['required', 'string'],
        ], [
            'contact_number.regex' => 'Phone number must use the format 09XX XXX XXXX.',
        ]);

        $updated = DB::table('customers')->where('customer_id', $customer)->update($validated);
        abort_if($updated === 0 && ! DB::table('customers')->where('customer_id', $customer)->exists(), 404);

        return back()->with('success', 'Customer information updated.');
    }

    public function destroy(Request $request, int $customer): RedirectResponse
    {
        abort_unless(in_array($request->user()?->role, ['manager', 'employee'], true), 403);

        $customerRecord = DB::table('customers')->where('customer_id', $customer)->first(['name']);
        abort_if($customerRecord === null, 404);
        $expectedName = trim($customerRecord->name);

        $request->merge(['confirmation_name' => $request->string('confirmation_name')->trim()->toString()]);
        $request->validate([
            'confirmation_name' => ['required', 'string', Rule::in([$expectedName])],
        ]);

        $deleted = DB::table('customers')->where('customer_id', $customer)->delete();
        abort_if($deleted === 0, 404);

        return back()->with('success', 'Customer and associated order history removed.');
    }
}
