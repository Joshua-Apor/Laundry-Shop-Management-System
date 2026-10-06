<?php

namespace App\Http\Controllers;

use App\Mail\EmployeeWelcome;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Throwable;

class ManagerController extends Controller
{
    public function customerRecords(Request $request): View
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

        return view('manager.customer-records', [
            'customers' => $customers,
            'search' => $search,
        ]);
    }

    public function ordersAndPayments(Request $request): View
    {
        abort_unless($request->user()?->role === 'manager', 403);

        $filterInput = $request->query();
        $validator = Validator::make($filterInput, [
            'search' => ['nullable', 'string', 'max:255'],
            'min_total' => ['nullable', 'numeric', 'min:0'],
            'max_total' => ['nullable', 'numeric', 'min:0', Rule::when($request->filled('min_total'), ['gte:min_total'])],
            'status' => ['nullable', Rule::in(Order::STATUSES)],
            'payment_status' => ['nullable', Rule::in(['Paid', 'Partial'])],
            'payment_method' => ['nullable', Rule::in(['Cash', 'GCash'])],
            'date_from' => ['nullable', 'date_format:Y-m-d'],
            'date_to' => ['nullable', 'date_format:Y-m-d', Rule::when($request->filled('date_from'), ['after_or_equal:date_from'])],
        ]);
        $filters = $validator->passes() ? $validator->validated() : [];
        $formFilters = collect($filterInput)
            ->map(fn ($value) => is_scalar($value) ? $value : '')
            ->all();
        $searchQuery = is_string($filters['search'] ?? null) ? trim($filters['search']) : '';
        $search = is_scalar($formFilters['search'] ?? null) ? trim((string) $formFilters['search']) : '';

        $orders = Order::query()
            ->withRecordDetails()
            ->when($searchQuery !== '', function ($query) use ($searchQuery): void {
                $query->where(function ($query) use ($searchQuery): void {
                    $query->where('customers.name', 'like', "%{$searchQuery}%")
                        ->orWhere('customers.contact_number', 'like', "%{$searchQuery}%")
                        ->orWhere('laundry_orders.order_id', 'like', "%{$searchQuery}%")
                        ->orWhere('users.name', 'like', "%{$searchQuery}%")
                        ->orWhereExists(function ($paymentQuery) use ($searchQuery): void {
                            $paymentQuery->selectRaw('1')
                                ->from('payments')
                                ->whereColumn('payments.order_id', 'laundry_orders.order_id')
                                ->where('payments.reference_number', 'like', "%{$searchQuery}%");
                        });
                });
            })
            ->when(isset($filters['min_total']), fn ($query) => $query->where('laundry_orders.total_amount', '>=', $filters['min_total']))
            ->when(isset($filters['max_total']), fn ($query) => $query->where('laundry_orders.total_amount', '<=', $filters['max_total']))
            ->when(isset($filters['status']), fn ($query) => $query->where('laundry_orders.order_status', $filters['status']))
            ->when(isset($filters['date_from']), fn ($query) => $query->where('laundry_orders.order_date', '>=', $filters['date_from']))
            ->when(isset($filters['date_to']), fn ($query) => $query->where('laundry_orders.order_date', '<=', $filters['date_to']))
            ->when(isset($filters['payment_status']) || isset($filters['payment_method']), function ($query) use ($filters): void {
                $query->whereExists(function ($paymentQuery) use ($filters): void {
                    $paymentQuery->selectRaw('1')
                        ->from('payments')
                        ->whereColumn('payments.order_id', 'laundry_orders.order_id')
                        ->whereIn('payments.payment_id', function ($latestPaymentQuery): void {
                            $latestPaymentQuery->selectRaw('MAX(latest_payments.payment_id)')
                                ->from('payments as latest_payments')
                                ->whereColumn('latest_payments.order_id', 'payments.order_id');
                        })
                        ->when(isset($filters['payment_status']), fn ($paymentQuery) => $paymentQuery->where('payments.payment_status', $filters['payment_status']))
                        ->when(isset($filters['payment_method']), fn ($paymentQuery) => $paymentQuery->where('payments.payment_method', $filters['payment_method']));
                });
            })
            ->latest('laundry_orders.order_date')
            ->orderByDesc('laundry_orders.order_id')
            ->paginate(15)
            ->withQueryString();

        return view('manager.orders-payments', [
            'orders' => $orders,
            'search' => $search,
            'filters' => $formFilters,
        ])->withErrors($validator);
    }

    public function employees(Request $request): View
    {
        abort_unless($request->user()?->role === 'manager', 403);

        $employees = User::query()
            ->where('role', 'employee')
            ->orderBy('name')
            ->get();

        $activeEmployeeIds = DB::table(config('session.table', 'sessions'))
            ->whereNotNull('user_id')
            ->where('last_activity', '>=', now()->subMinutes(config('session.lifetime'))->timestamp)
            ->pluck('user_id')
            ->unique();

        $employees->each(function (User $employee) use ($activeEmployeeIds): void {
            $employee->setAttribute('is_logged_in', $activeEmployeeIds->contains($employee->getKey()));
        });

        return view('manager.employees', ['employees' => $employees]);
    }

    public function storeEmployee(Request $request): RedirectResponse
    {
        abort_unless($request->user()?->role === 'manager', 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users,username'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
        ]);

        $password = Str::password(16);
        $employee = User::query()->create([
            'name' => $validated['name'],
            'username' => $validated['username'],
            'email' => $validated['email'],
            'password' => $password,
            'role' => 'employee',
            'must_change_password' => true,
        ]);

        try {
            Mail::to($employee->email)->send(new EmployeeWelcome(
                $employee->name,
                $employee->username,
                $password,
            ));
        } catch (Throwable $exception) {
            report($exception);
            $employee->delete();

            return back()->withInput()->withErrors([
                'email' => 'The employee account was not created because the welcome email could not be sent. Check the mail settings and try again.',
            ]);
        }

        return redirect()->route('manager.employees')->with('success', 'Employee account created and email sent.');
    }

    public function destroyEmployee(Request $request, User $employee): RedirectResponse
    {
        abort_unless($request->user()?->role === 'manager', 403);
        abort_unless($employee->role === 'employee', 404);

        DB::table(config('session.table', 'sessions'))->where('user_id', $employee->getKey())->delete();
        $employee->forceDelete();

        return redirect()->route('manager.employees')->with('success', 'Employee permanently deleted.');
    }
}
