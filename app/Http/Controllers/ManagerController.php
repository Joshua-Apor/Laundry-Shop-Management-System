<?php

namespace App\Http\Controllers;

use App\Mail\EmployeeWelcome;
use App\Models\Order;
use App\Models\User;
use Carbon\CarbonImmutable;
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
        $sort = $request->string('sort')->toString();
        $sort = in_array($sort, ['alphabetical', 'newest', 'oldest'], true) ? $sort : 'alphabetical';
        $filterInput = $request->query();
        $validator = Validator::make($filterInput, [
            'activity' => ['nullable', 'in:all,today,week,custom'],
            'date_from' => ['nullable', 'date_format:Y-m-d'],
            'date_to' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:date_from'],
        ]);
        $dateFilters = $validator->passes() ? $validator->validated() : [];
        $activity = $dateFilters['activity'] ?? 'all';
        $today = CarbonImmutable::today();
        [$dateFrom, $dateTo] = match ($activity) {
            'today' => [$today->toDateString(), $today->toDateString()],
            'week' => [$today->startOfWeek()->toDateString(), $today->endOfWeek()->toDateString()],
            'custom' => [$dateFilters['date_from'] ?? null, $dateFilters['date_to'] ?? null],
            default => [null, null],
        };

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

        return view('manager.customer-records', [
            'customers' => $customers,
            'search' => $search,
            'sort' => $sort,
            'activity' => $activity,
            'dateFrom' => $dateFilters['date_from'] ?? $dateFrom,
            'dateTo' => $dateFilters['date_to'] ?? $dateTo,
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
            'status' => ['nullable', Rule::in(Order::ALL_STATUSES)],
            'payment_status' => ['nullable', Rule::in(['Paid', 'Partial'])],
            'payment_method' => ['nullable', Rule::in(['Cash', 'GCash'])],
            'date_from' => ['nullable', 'date_format:Y-m-d'],
            'date_to' => ['nullable', 'date_format:Y-m-d', Rule::when($request->filled('date_from'), ['after_or_equal:date_from'])],
            'date_preset' => ['nullable', Rule::in(['today', 'week', 'all', 'custom'])],
        ]);
        $filters = $validator->passes() ? $validator->validated() : [];
        $formFilters = collect($filterInput)
            ->map(fn ($value) => is_scalar($value) ? $value : '')
            ->all();
        $searchQuery = is_string($filters['search'] ?? null) ? trim($filters['search']) : '';
        $search = is_scalar($formFilters['search'] ?? null) ? trim((string) $formFilters['search']) : '';
        $view = ($filterInput['view'] ?? 'active') === 'history' ? 'history' : 'active';
        $datePreset = $filters['date_preset'] ?? ($view === 'active' ? 'today' : 'all');
        if ($datePreset === 'today') {
            $filters['date_from'] = now()->toDateString();
            $filters['date_to'] = now()->toDateString();
        } elseif ($datePreset === 'week') {
            $filters['date_from'] = now()->startOfWeek()->toDateString();
            $filters['date_to'] = now()->endOfWeek()->toDateString();
        } elseif ($datePreset === 'all') {
            unset($filters['date_from'], $filters['date_to']);
        } elseif ($datePreset === 'custom' && empty($filters['date_from']) && empty($filters['date_to'])) {
            $filters['date_from'] = now()->toDateString();
            $filters['date_to'] = now()->toDateString();
        }
        $formFilters['date_preset'] = $datePreset;
        $formFilters['date_from'] = $filters['date_from'] ?? '';
        $formFilters['date_to'] = $filters['date_to'] ?? '';

        $orders = Order::query()
            ->withRecordDetails()
            ->when($view === 'history', function ($query): void {
                $query->whereIn('laundry_orders.order_status', Order::HISTORY_STATUSES);
            }, function ($query): void {
                $query->whereNotIn('laundry_orders.order_status', Order::HISTORY_STATUSES);
            })
            ->when($searchQuery !== '', function ($query) use ($searchQuery): void {
                $query->where(function ($query) use ($searchQuery): void {
                    $query->where('customers.name', 'like', "%{$searchQuery}%")
                        ->orWhere('customers.contact_number', 'like', "%{$searchQuery}%")
                        ->orWhere('laundry_orders.order_id', 'like', "%{$searchQuery}%")
                        ->orWhere('laundry_orders.employee_name', 'like', "%{$searchQuery}%")
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
            ->orderByDesc('laundry_orders.order_date')
            ->orderByDesc('laundry_orders.order_time')
            ->orderByDesc('laundry_orders.order_id')
            ->paginate(15)
            ->withQueryString();

        $paymentsByOrder = DB::table('payments')
            ->whereIn('order_id', $orders->getCollection()->pluck('order_id'))
            ->orderBy('payment_id')
            ->get(['payment_id', 'order_id', 'payment_date', 'amount', 'payment_method', 'payment_status', 'reference_number'])
            ->groupBy('order_id');

        $orders->getCollection()->each(function (Order $order) use ($paymentsByOrder): void {
            $order->setAttribute('recordPayments', $paymentsByOrder->get($order->order_id, collect())->values());
        });

        return view('manager.orders-payments', [
            'orders' => $orders,
            'search' => $search,
            'filters' => $formFilters,
            'view' => $view,
            'datePreset' => $datePreset,
        ])->withErrors($validator);
    }

    public function employees(Request $request): View
    {
        abort_unless($request->user()?->role === 'manager', 403);

        $search = trim((string) $request->query('search', ''));

        $employees = User::query()
            ->where('role', 'employee')
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('username', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->orderBy('name')
            ->get();

        $employeeIdsWithOrders = DB::table('laundry_orders')
            ->whereIn('user_id', $employees->modelKeys())
            ->distinct()
            ->pluck('user_id');

        $activeEmployeeIds = DB::table(config('session.table', 'sessions'))
            ->whereNotNull('user_id')
            ->where('last_activity', '>=', now()->subMinutes(config('session.lifetime'))->timestamp)
            ->pluck('user_id')
            ->unique();

        $employees->each(function (User $employee) use ($activeEmployeeIds, $employeeIdsWithOrders): void {
            $employee->setAttribute('is_logged_in', $activeEmployeeIds->contains($employee->getKey()));
            $employee->setAttribute('has_orders', $employeeIdsWithOrders->contains($employee->getKey()));
        });

        return view('manager.employees', [
            'employees' => $employees,
            'search' => $search,
        ]);
    }

    public function storeEmployee(Request $request): RedirectResponse
    {
        abort_unless($request->user()?->role === 'manager', 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users,username'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
        ]);

        $password = Str::random(8);
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

            return back()->withInput()->withErrors([
                'email' => 'The employee account was created, but the welcome email could not be sent. Check the mail configuration and application logs.',
            ]);
        }

        $successMessage = config('mail.default') === 'log'
            ? 'Employee account created. The welcome email was written to the application log.'
            : 'Employee account created and welcome email sent.';

        return redirect()->route('manager.employees')->with('success', $successMessage);
    }

    public function destroyEmployee(Request $request, User $employee): RedirectResponse
    {
        abort_unless($request->user()?->role === 'manager', 403);
        abort_unless($employee->role === 'employee', 404);

        if (DB::table('laundry_orders')->where('user_id', $employee->getKey())->exists()) {
            return redirect()->route('manager.employees')->withErrors([
                'employee' => 'This employee cannot be deleted because they have order records.',
            ]);
        }

        $request->merge(['confirmation_name' => $request->string('confirmation_name')->trim()->toString()]);
        $request->validate([
            'confirmation_name' => ['required', 'string', Rule::in([trim($employee->name)])],
        ]);

        DB::table(config('session.table', 'sessions'))
            ->where('user_id', $employee->getKey())
            ->delete();

        $employee->forceDelete();

        return redirect()->route('manager.employees')->with('success', 'Employee deleted.');
    }

    public function updateEmployeeAccess(Request $request, User $employee): RedirectResponse
    {
        abort_unless($request->user()?->role === 'manager', 403);
        abort_unless($employee->role === 'employee', 404);

        $validated = $request->validate([
            'account_status' => ['required', 'boolean'],
        ]);

        $employee->account_status = (bool) $validated['account_status'];
        $employee->save();

        if (! $employee->account_status) {
            DB::table(config('session.table', 'sessions'))
                ->where('user_id', $employee->getKey())
                ->delete();
        }

        $message = $employee->account_status
            ? 'Employee access restored.'
            : 'Employee access revoked and active sessions signed out.';

        return redirect()->route('manager.employees')->with('success', $message);
    }
}
