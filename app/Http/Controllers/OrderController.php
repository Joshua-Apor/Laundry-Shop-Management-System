<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function create(): View
    {
        return view('employee.orders.create');
    }

    public function index(Request $request): View
    {
        $search = $request->string('search')->trim()->toString();
        $status = $request->string('status')->trim()->toString();

        $orders = Order::query()
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('fullname', 'like', "%{$search}%")
                        ->orWhere('phoneNumber', 'like', "%{$search}%");

                    if (is_numeric($search)) {
                        $query->orWhereKey((int) $search);
                    }
                });
            })
            ->when(in_array($status, Order::STATUSES, true), function ($query) use ($status): void {
                $query->where('status', $status);
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('employee.orders.index', [
            'orders' => $orders,
            'search' => $search,
            'status' => $status,
        ]);
    }

    public function show(Order $order): View
    {
        return view('employee.orders.show', ['order' => $order]);
    }

    public function store(StoreOrderRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $ratePerKg = 60;
        $total = $validated['weight'] * $ratePerKg;
        if (in_array('Ironing', $validated['services'], true)) {
            $total += 30;
        }

        Order::create([
            'fullname' => $validated['full_name'],
            'phoneNumber' => $validated['phone_number'],
            'service' => $validated['services'],
            'weight' => $validated['weight'],
            'specialRequest' => $validated['special_request'] ?? null,
            'paymentMethod' => $validated['payment_method'],
            'amount_paid' => $validated['amount_paid'] ?? 0,
            'total_amount' => $total,
            'status' => 'Received',
        ]);

        return redirect()->route('orders.index')->with('success', 'Order created successfully.');
    }

    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'string', Rule::in(Order::STATUSES)],
        ]);

        $order->update(['status' => $validated['status']]);

        return redirect()->route('orders.index')->with('success', "Order #{$order->id} moved to {$order->status}.");
    }

    public function recordPayment(Order $order): RedirectResponse
    {
        $order->update(['amount_paid' => $order->total_amount]);

        return redirect()->route('orders.index')->with('success', "Payment recorded for order #{$order->id}.");
    }
}
