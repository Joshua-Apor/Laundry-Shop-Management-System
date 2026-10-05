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

        $weight = (float) $validated['weight'];
        $laundryAmount = (float) ($validated['laundry_amount'] ?? ($weight * 60));
        $selectedServices = DB::table('services')
            ->whereIn('service_id', $validated['services'])
            ->get(['service_id', 'service_name', 'base_price', 'price_unit']);
        $serviceFee = (float) $selectedServices->sum('base_price');
        $specialRequestPrice = (float) ($validated['special_request_price'] ?? 0);
        $total = $laundryAmount + $serviceFee + $specialRequestPrice;
        $amountPaid = (float) ($validated['amount_paid'] ?? 0);
        $orderCreatedAt = now();

        DB::transaction(function () use ($validated, $weight, $total, $amountPaid, $orderCreatedAt, $selectedServices): void {
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
                'order_date' => $orderCreatedAt->toDateString(),
                'order_time' => $orderCreatedAt->format('H:i:s'),
                'laundry_weight' => $weight,
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
                    'quantity' => 1,
                    'service_fee' => $service->base_price,
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

        $validated = $request->validate([
            'status' => ['required', 'string', Rule::in(Order::STATUSES)],
        ]);

        Order::query()
            ->whereKey($order->getKey())
            ->update(['order_status' => $validated['status']]);

        return back()->with('success', "Order #{$order->order_id} moved to {$validated['status']}.");
    }

    public function recordPayment(Order $order): RedirectResponse
    {
        $order->update(['amount_paid' => $order->total_amount]);

        return redirect()->route('records.index')->with('success', "Payment recorded for order #{$order->id}.");
    }
}
