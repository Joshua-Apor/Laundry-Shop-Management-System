<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function create(): View
    {
        abort_unless(auth()->user()?->role === 'employee', 403);

        $customers = DB::table('customers')
            ->select(['customer_id', 'name', 'contact_number', 'address'])
            ->orderBy('name')
            ->get();

        $services = DB::table('services')
            ->whereNull('deleted_at')
            ->orderBy('service_name')
            ->get(['service_id', 'service_name', 'base_price', 'price_unit']);

        return view('employee.records.create', ['customers' => $customers, 'services' => $services]);
    }

    public function index(Request $request): View
    {
        $search = $request->string('search')->trim()->toString();
        $status = $request->string('status')->trim()->toString();

        $orders = Order::query()
            ->withRecordDetails()
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('customers.name', 'like', "%{$search}%")
                        ->orWhere('customers.contact_number', 'like', "%{$search}%");

                    if (is_numeric($search)) {
                        $query->orWhere('laundry_orders.order_id', (int) $search);
                    }
                });
            })
            ->when(in_array($status, Order::STATUSES, true), function ($query) use ($status): void {
                $query->where('laundry_orders.order_status', $status);
            })
            ->latest('laundry_orders.order_date')
            ->paginate(15)
            ->withQueryString();

        return view('employee.records.index', [
            'orders' => $orders,
            'search' => $search,
            'status' => $status,
        ]);
    }

    public function show(Order $order): View
    {
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
            ->get(['service_id', 'service_name', 'base_price', 'price_unit']);
        $serviceUnits = $selectedServices->mapWithKeys(function (object $service) use ($weight, $selfServiceLoads, $serviceQuantities): array {
            $quantity = match ($service->service_name) {
                'Drop Off' => $weight,
                'Self Service' => (int) $selfServiceLoads,
                default => (int) ($serviceQuantities[$service->service_id] ?? 1),
            };

            return [$service->service_id => $quantity];
        });
        $serviceFees = $selectedServices->mapWithKeys(function (object $service) use ($weight, $serviceUnits): array {
            $fee = $service->service_name === 'Drop Off' && $weight !== null && $weight <= 5
                ? 175
                : (float) $service->base_price * $serviceUnits[$service->service_id];

            return [$service->service_id => $fee];
        });
        $serviceFee = (float) $serviceFees->sum();
        $specialRequestPrice = (float) ($validated['special_request_price'] ?? 0);
        $total = $serviceFee + $specialRequestPrice;
        $amountPaid = (float) ($validated['amount_paid'] ?? 0);

        if (round($amountPaid * 100) > round($total * 100)) {
            throw ValidationException::withMessages([
                'amount_paid' => 'The amount paid cannot exceed the order total.',
            ]);
        }

        $orderCreatedAt = now();
        $employeeName = $request->user()->name;

        DB::transaction(function () use ($validated, $weight, $selfServiceLoads, $serviceUnits, $serviceFees, $total, $amountPaid, $orderCreatedAt, $employeeName, $selectedServices): void {
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
                    'address' => $validated['address'] ?? '',
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

            foreach ($selectedServices as $service) {
                DB::table('order_services')->insert([
                    'order_id' => $orderId,
                    'service_id' => $service->service_id,
                    'quantity' => $service->service_name === 'Drop Off' ? 1 : $serviceUnits[$service->service_id],
                    'service_fee' => $serviceFees[$service->service_id],
                ]);
            }

            if ($amountPaid > 0) {
                DB::table('payments')->insert([
                    'order_id' => $orderId,
                    'payment_date' => now()->toDateString(),
                    'amount' => $amountPaid,
                    'payment_method' => $validated['payment_method'],
                    'payment_status' => $amountPaid >= $total ? 'Paid' : 'Partial',
                    'reference_number' => null,
                ]);
            }
        });

        return redirect()->route('records.index')->with('success', 'Order created successfully.');
    }

    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        abort_unless($request->user()?->role === 'employee', 403);

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

        $updated = $statusUpdate->update(['order_status' => $nextStatus]);

        if ($updated === 0) {
            throw ValidationException::withMessages([
                'status' => $nextStatus === 'Completed'
                    ? 'This order must be fully paid before it can be completed.'
                    : 'This order status has changed. Refresh the page and try again.',
            ]);
        }

        return back()->with('success', "Order #{$order->order_id} moved to {$nextStatus}.");
    }

    public function recordPayment(Request $request, Order $order): RedirectResponse
    {
        abort_unless(auth()->user()?->role === 'employee', 403);

        $validated = $request->validate([
            'payment_method' => ['required', 'string', Rule::in(['Cash', 'GCash'])],
        ]);

        DB::transaction(function () use ($order, $validated): void {
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

            DB::table('payments')->insert([
                'order_id' => $order->getKey(),
                'payment_date' => now()->toDateString(),
                'amount' => $balance,
                'payment_method' => $validated['payment_method'],
                'payment_status' => 'Paid',
            ]);
        });

        return back()->with('success', "Outstanding balance paid for order #{$order->getKey()}.");
    }
}
