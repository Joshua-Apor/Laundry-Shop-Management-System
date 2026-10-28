<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Carbon\CarbonImmutable;
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
        $sort = $request->string('sort')->toString();
        $sort = in_array($sort, ['alphabetical', 'newest', 'oldest'], true) ? $sort : 'alphabetical';
        $filterInput = $request->query();
        $validator = validator($filterInput, [
            'activity' => ['nullable', 'in:all,today,week,custom'],
            'date_from' => ['nullable', 'date_format:Y-m-d'],
            'date_to' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:date_from'],
        ]);
        $dateFilters = $validator->passes() ? $validator->validated() : [];
        $activity = $dateFilters['activity'] ?? 'all';
        [$dateFrom, $dateTo] = $this->customerActivityRange($activity, $dateFilters);

        [$sortColumn, $sortDirection] = match ($sort) {
            'alphabetical' => ['customers.name', 'asc'],
            'oldest' => ['first_order_date', 'asc'],
            default => ['first_order_date', 'desc'],
        };

        $customers = Order::query()
            ->leftJoin('customers', 'customers.customer_id', '=', 'laundry_orders.customer_id')
            ->whereNull('customers.deleted_at')
            ->select([
                'customers.customer_id as customer_id',
                'customers.name as fullname',
                'customers.contact_number as phoneNumber',
                'customers.email as email',
            ])
            ->selectRaw('COUNT(laundry_orders.order_id) as orders_count, SUM(laundry_orders.total_amount) as total_spent, MIN(laundry_orders.order_date) as first_order_date, MAX(laundry_orders.order_date) as latest_order_date')
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('customers.name', 'like', "%{$search}%")
                        ->orWhere('customers.contact_number', 'like', "%{$search}%");
                });
            })
            ->when($dateFrom !== null, fn ($query) => $query->whereExists(function ($orders) use ($dateFrom, $dateTo): void {
                $orders->selectRaw('1')->from('laundry_orders as activity_orders')
                    ->whereColumn('activity_orders.customer_id', 'customers.customer_id')
                    ->whereBetween('activity_orders.order_date', [$dateFrom, $dateTo]);
            }))
            ->groupBy('customers.customer_id', 'customers.name', 'customers.contact_number', 'customers.email')
            ->orderBy($sortColumn, $sortDirection)
            ->orderBy('customers.name')
            ->paginate(15)
            ->withQueryString();

        return view('employee.customers.index', [
            'customers' => $customers,
            'search' => $search,
            'sort' => $sort,
            'activity' => $activity,
            'dateFrom' => $dateFilters['date_from'] ?? $dateFrom,
            'dateTo' => $dateFilters['date_to'] ?? $dateTo,
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
            ->paginate(15)
            ->withQueryString();

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
            'email' => ['nullable', 'email', 'max:255'],
        ], [
            'contact_number.regex' => 'Phone number must use the format 09XX-XXX-XXXX.',
        ]);

        $updated = DB::table('customers')->where('customer_id', $customer)->update($validated);
        abort_if($updated === 0 && ! DB::table('customers')->where('customer_id', $customer)->exists(), 404);

        return back()->with('success', 'Customer information updated.');
    }

    /**
     * @param  array{date_from?: string, date_to?: string}  $dateFilters
     * @return array{?string, ?string}
     */
    private function customerActivityRange(string $activity, array $dateFilters): array
    {
        $today = CarbonImmutable::today();

        return match ($activity) {
            'today' => [$today->toDateString(), $today->toDateString()],
            'week' => [$today->startOfWeek()->toDateString(), $today->endOfWeek()->toDateString()],
            'custom' => [$dateFilters['date_from'] ?? $today->toDateString(), $dateFilters['date_to'] ?? $today->toDateString()],
            default => [null, null],
        };
    }
}
