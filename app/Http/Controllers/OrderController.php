<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Mail\OrderReadyForPickup;
use App\Mail\PaymentReceipt;
use App\Models\Order;
use App\Services\OrderStorageFeeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Throwable;

class OrderController extends Controller
{
    public function create(): View
    {
        abort_unless(auth()->user()?->role === 'employee', 403);

        $customers = DB::table('customers')
            ->whereNull('deleted_at')
            ->select(['customer_id', 'name', 'contact_number', 'email'])
            ->orderBy('name')
            ->get();

        $services = DB::table('services')
            ->whereNull('deleted_at')
            ->orderBy('service_name')
            ->get(['service_id', 'service_name', 'base_price', 'price_unit', 'available_for']);

        return view('employee.records.create', ['customers' => $customers, 'services' => $services]);
    }

    public function statusQueue(Request $request): View
    {
        abort_unless($request->user()?->role === 'employee', 403);

        $search = $request->string('search')->trim()->toString();
        $orders = Order::query()
            ->withRecordDetails()
            ->whereIn('laundry_orders.order_status', ['Processing', 'Ready for Pickup'])
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('customers.name', 'like', "%{$search}%")
                        ->orWhere('customers.contact_number', 'like', "%{$search}%")
                        ->orWhere('laundry_orders.order_id', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('laundry_orders.order_date')
            ->orderByDesc('laundry_orders.order_time')
            ->orderByDesc('laundry_orders.order_id')
            ->paginate(15)
            ->withQueryString();

        return view('employee.orders.status', [
            'orders' => $orders,
            'search' => $search,
            'receipt' => $this->paymentReceipt($request),
        ]);
    }

    public function index(Request $request): View
    {
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

        return view('employee.records.index', [
            'orders' => $orders,
            'search' => $search,
            'filters' => $formFilters,
            'view' => $view,
            'datePreset' => $datePreset,
            'receipt' => $this->paymentReceipt($request),
        ])->withErrors($validator);
    }

    /**
     * @return array{order: Order, payment: object}|null
     */
    private function paymentReceipt(Request $request): ?array
    {
        $receiptIds = $request->session()->pull('payment_receipt');

        if (! is_array($receiptIds) || ! isset($receiptIds['order_id'], $receiptIds['payment_id'])) {
            return null;
        }

        $orderId = (int) $receiptIds['order_id'];
        $paymentId = (int) $receiptIds['payment_id'];

        if ($orderId < 1 || $paymentId < 1) {
            return null;
        }

        return $this->paymentReceiptData($orderId, $paymentId);
    }

    /**
     * @return array{order: Order, payment: object}|null
     */
    private function paymentReceiptData(int $orderId, int $paymentId): ?array
    {
        $order = Order::query()
            ->withRecordDetails()
            ->where('laundry_orders.order_id', $orderId)
            ->first();
        $payment = DB::table('payments')
            ->where('order_id', $orderId)
            ->where('payment_id', $paymentId)
            ->first(['payment_id', 'payment_date', 'amount', 'payment_method', 'payment_status', 'reference_number']);

        if ($order === null || $payment === null) {
            return null;
        }

        return ['order' => $order, 'payment' => $payment];
    }

    private function sendPaymentReceiptEmail(int $orderId, int $paymentId): ?bool
    {
        $receipt = $this->paymentReceiptData($orderId, $paymentId);

        if ($receipt === null || blank($receipt['order']->customerEmail)) {
            return null;
        }

        try {
            Mail::to($receipt['order']->customerEmail)->send(
                new PaymentReceipt($receipt['order'], $receipt['payment']),
            );
        } catch (Throwable $exception) {
            report($exception);

            return false;
        }

        return true;
    }

    public function show(Order $order): View
    {
        $order = Order::query()
            ->withRecordDetails()
            ->where('laundry_orders.order_id', $order->getKey())
            ->firstOrFail();

        return view('employee.records.show', ['order' => $order]);
    }

    public function store(StoreOrderRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $weight = isset($validated['weight']) ? (float) $validated['weight'] : null;
        $selfServiceLoads = $validated['self_service_loads'] ?? null;
        $serviceQuantities = $validated['service_quantities'] ?? [];
        $selectedServices = DB::table('services')
            ->whereIn('service_id', $validated['services'])
            ->get(['service_id', 'service_name', 'base_price', 'price_unit', 'available_for']);
        $serviceUnits = $selectedServices->mapWithKeys(function (object $service) use ($weight, $selfServiceLoads, $serviceQuantities): array {
            $quantity = match ($service->service_name) {
                'Drop Off' => $weight,
                'Self Service' => (int) $selfServiceLoads,
                default => (int) ($serviceQuantities[$service->service_id] ?? 1),
            };

            return [$service->service_id => $quantity];
        });
        $serviceFees = $selectedServices->mapWithKeys(function (object $service) use ($weight, $serviceUnits): array {
            $fee = $service->service_name === 'Drop Off' && $weight !== null
                ? 175 + max((int) floor($weight) - 5, 0) * (float) $service->base_price
                : (float) $service->base_price * $serviceUnits[$service->service_id];

            return [$service->service_id => $fee];
        });
        $serviceFee = (float) $serviceFees->sum();
        $specialRequest = trim((string) ($validated['special_request'] ?? ''));
        $specialRequestPrice = (float) ($validated['special_request_price'] ?? 0);
        $total = $serviceFee + $specialRequestPrice;
        $amountPaid = (float) ($validated['amount_paid'] ?? 0);

        if (round($amountPaid * 100) > round($total * 100)) {
            throw ValidationException::withMessages([
                'amount_paid' => 'The amount paid cannot exceed the order total.',
            ]);
        }

        $orderCreatedAt = now();
        $paymentRecordedAt = now();
        $employeeName = $request->user()->name;

        $receipt = DB::transaction(function () use ($validated, $weight, $selfServiceLoads, $serviceUnits, $serviceFees, $total, $amountPaid, $orderCreatedAt, $paymentRecordedAt, $employeeName, $selectedServices, $specialRequest, $specialRequestPrice): array {
            if (isset($validated['customer_id'])) {
                $customer = DB::table('customers')
                    ->where('customer_id', $validated['customer_id'])
                    ->first(['customer_id', 'contact_number']);

                if ($customer === null || $customer->contact_number !== $validated['phone_number']) {
                    throw ValidationException::withMessages([
                        'phone_number' => 'The selected customer does not match this phone number. Choose the customer again.',
                    ]);
                }

                $customerId = (int) $customer->customer_id;
            } else {
                $existingCustomer = DB::table('customers')
                    ->where('contact_number', $validated['phone_number'])
                    ->lockForUpdate()
                    ->first(['customer_id']);

                if ($existingCustomer !== null) {
                    throw ValidationException::withMessages([
                        'phone_number' => 'This phone number is already registered. Choose the existing customer or enter a different number.',
                    ]);
                }

                $customerId = DB::table('customers')->insertGetId([
                    'name' => $validated['full_name'],
                    'contact_number' => $validated['phone_number'],
                    'email' => $validated['email'] ?? null,
                ]);
            }

            $orderId = DB::table('laundry_orders')->insertGetId([
                'customer_id' => $customerId,
                'user_id' => auth()->id(),
                'employee_name' => $employeeName,
                'order_date' => $orderCreatedAt->toDateString(),
                'order_time' => $orderCreatedAt->format('H:i:s'),
                'laundry_weight' => $weight,
                'order_type' => $validated['order_type'],
                'self_service_loads' => $selfServiceLoads,
                'total_amount' => $total,
                'amount_paid' => $amountPaid,
                'balance' => max($total - $amountPaid, 0),
                'order_status' => 'Processing',
                'pickup_date' => null,
            ]);

            if ($specialRequest !== '' || $specialRequestPrice > 0) {
                DB::table('special_requests')->insert([
                    'order_id' => $orderId,
                    'request_details' => $specialRequest,
                    'additional_fee' => $specialRequestPrice,
                ]);
            }

            foreach ($selectedServices as $service) {
                DB::table('order_services')->insert([
                    'order_id' => $orderId,
                    'service_id' => $service->service_id,
                    'quantity' => $service->service_name === 'Drop Off' ? 1 : $serviceUnits[$service->service_id],
                    'service_fee' => $serviceFees[$service->service_id],
                ]);
            }

            $paymentId = null;

            if ($amountPaid > 0) {
                $receiptNumber = $this->nextPaymentReference($paymentRecordedAt);
                $paymentId = DB::table('payments')->insertGetId([
                    'order_id' => $orderId,
                    'payment_date' => $paymentRecordedAt->toDateString(),
                    'amount' => $amountPaid,
                    'payment_method' => $validated['payment_method'],
                    'payment_status' => $amountPaid >= $total ? 'Paid' : 'Partial',
                    'reference_number' => $receiptNumber,
                ]);

                DB::table('claim_receipts')->insert([
                    'order_id' => $orderId,
                    'receipt_number' => $receiptNumber,
                    'issue_date' => $paymentRecordedAt->toDateString(),
                ]);
            }

            return [
                'order_id' => (int) $orderId,
                'payment_id' => $paymentId === null ? null : (int) $paymentId,
            ];
        });

        $emailSent = $receipt['payment_id'] === null
            ? null
            : $this->sendPaymentReceiptEmail($receipt['order_id'], $receipt['payment_id']);

        $redirect = redirect()->route('records.index')
            ->with('success', 'Order created successfully.')
            ->with('payment_receipt', $receipt);

        if ($emailSent === false) {
            $redirect->with('receipt_email_warning', 'The payment was recorded, but the receipt email could not be sent. Check the mail configuration.');
        }

        return $redirect;
    }

    public function updateStatus(
        Request $request,
        Order $order,
        OrderStorageFeeService $storageFeeService,
    ): RedirectResponse {
        abort_unless($request->user()?->role === 'employee', 403);

        if ($order->order_status === 'Ready for Pickup') {
            $storageFeeService->accrueForOrder((int) $order->getKey());
            $order->refresh();
        }

        $currentStatus = $order->order_status;
        $currentStatusIndex = array_search($currentStatus, Order::STATUSES, true);

        abort_unless($currentStatusIndex !== false && isset(Order::STATUSES[$currentStatusIndex + 1]), 422);

        $nextStatus = Order::STATUSES[$currentStatusIndex + 1];
        $statusUpdate = DB::table('laundry_orders')
            ->where('order_id', $order->getKey())
            ->where('order_status', $currentStatus);

        if ($nextStatus === 'Completed') {
            $statusUpdate->where('balance', '<=', 0);
        }

        $statusValues = ['order_status' => $nextStatus];

        if ($nextStatus === 'Ready for Pickup') {
            $statusValues['ready_for_pickup_at'] = now();
            $statusValues['storage_fee'] = 0;
        }

        $updated = $statusUpdate->update($statusValues);

        if ($updated === 0) {
            throw ValidationException::withMessages([
                'status' => $nextStatus === 'Completed'
                    ? 'This order must be fully paid before it can be completed.'
                    : 'This order status has changed. Refresh the page and try again.',
            ]);
        }

        $redirect = back()->with('success', "Order #{$order->order_id} moved to {$nextStatus}.");

        if ($nextStatus === 'Ready for Pickup') {
            $customer = DB::table('customers')
                ->where('customer_id', $order->customer_id)
                ->first(['name', 'email']);

            if ($customer === null || blank($customer->email)) {
                $redirect->with('ready_for_pickup_email_warning', 'The order is ready for pickup, but the customer does not have an email address.');
            } else {
                $remainingBalance = (float) DB::table('laundry_orders')
                    ->where('order_id', $order->getKey())
                    ->value('balance');

                try {
                    Mail::to($customer->email)->send(new OrderReadyForPickup(
                        (string) $customer->name,
                        (int) $order->getKey(),
                        $remainingBalance,
                    ));
                } catch (Throwable $exception) {
                    report($exception);
                    $redirect->with('ready_for_pickup_email_warning', 'The order is ready for pickup, but the email could not be sent. Check the mail configuration.');
                }
            }
        }

        return $redirect;
    }

    public function recordPayment(Request $request, Order $order, OrderStorageFeeService $storageFeeService): RedirectResponse
    {
        abort_unless(auth()->user()?->role === 'employee', 403);

        $validated = $request->validate([
            'payment_method' => ['required', 'string', Rule::in(['Cash', 'GCash'])],
        ]);

        $storageFeeService->accrueForOrder((int) $order->getKey());

        $paymentRecordedAt = now();
        $paymentId = DB::transaction(function () use ($order, $validated, $paymentRecordedAt): int {
            $lockedOrder = DB::table('laundry_orders')
                ->where('order_id', $order->getKey())
                ->lockForUpdate()
                ->first(['total_amount', 'amount_paid', 'order_status']);

            $balance = max(round((float) $lockedOrder->total_amount - (float) $lockedOrder->amount_paid, 2), 0);

            if ($balance <= 0) {
                throw ValidationException::withMessages([
                    'payment_method' => 'This order has no outstanding balance.',
                ]);
            }

            DB::table('laundry_orders')
                ->where('order_id', $order->getKey())
                ->update([
                    'amount_paid' => $lockedOrder->total_amount,
                    'balance' => 0,
                    'order_status' => $lockedOrder->order_status === 'Ready for Pickup'
                        ? 'Completed'
                        : $lockedOrder->order_status,
                ]);

            $receiptNumber = $this->nextPaymentReference($paymentRecordedAt);
            $paymentId = (int) DB::table('payments')->insertGetId([
                'order_id' => $order->getKey(),
                'payment_date' => $paymentRecordedAt->toDateString(),
                'amount' => $balance,
                'payment_method' => $validated['payment_method'],
                'payment_status' => 'Paid',
                'reference_number' => $receiptNumber,
            ]);

            DB::table('claim_receipts')->insert([
                'order_id' => $order->getKey(),
                'receipt_number' => $receiptNumber,
                'issue_date' => $paymentRecordedAt->toDateString(),
            ]);

            return $paymentId;
        });

        $emailSent = $this->sendPaymentReceiptEmail((int) $order->getKey(), $paymentId);
        $redirect = back()
            ->with('success', "Outstanding balance paid for order #{$order->getKey()}.")
            ->with('payment_receipt', [
                'order_id' => (int) $order->getKey(),
                'payment_id' => $paymentId,
            ]);

        if ($emailSent === false) {
            $redirect->with('receipt_email_warning', 'The payment was recorded, but the receipt email could not be sent. Check the mail configuration.');
        }

        return $redirect;
    }

    private function nextPaymentReference(Carbon $paymentRecordedAt): string
    {
        $date = $paymentRecordedAt->toDateString();

        DB::table('payment_receipt_sequences')->insertOrIgnore([
            'receipt_date' => $date,
            'last_number' => 0,
        ]);

        $sequence = DB::table('payment_receipt_sequences')
            ->where('receipt_date', $date)
            ->lockForUpdate()
            ->first();
        $nextNumber = (int) $sequence->last_number + 1;

        DB::table('payment_receipt_sequences')
            ->where('receipt_date', $date)
            ->update(['last_number' => $nextNumber]);

        return $paymentRecordedAt->format('md-y').'-'.str_pad((string) $nextNumber, 4, '0', STR_PAD_LEFT);
    }
}
