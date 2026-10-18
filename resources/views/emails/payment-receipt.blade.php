<div>
    <!-- If you do not have a consistent goal in life, you can not live it in a consistent way. - Marcus Aurelius -->
</div>
@php
    $receiptNumber = $payment->reference_number ?: 'Not recorded';
    $paymentLabel = (int) $order->balancePaymentId === (int) $payment->payment_id
        ? 'Outstanding balance payment'
        : 'Initial payment';
    $previouslyPaid = max((float) $order->amount_paid - (float) $payment->amount, 0);
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment receipt {{ $receiptNumber }}</title>
</head>
<body style="margin: 0; padding: 24px; background: #f0f8ff; color: #0f172a; font-family: Arial, sans-serif;">
    <main style="max-width: 600px; margin: 0 auto; padding: 28px; border: 1px solid #dbeafe; border-radius: 16px; background: #ffffff;">
        <header style="padding-bottom: 20px; border-bottom: 1px dashed #bae6fd; text-align: center;">
            <img src="{{ $message->embed(public_path('images/logo.png')) }}" alt="I Laba U" width="64" style="display: block; width: 64px; height: auto; margin: 0 auto 8px;">
            <h1 style="margin: 0; font-size: 22px;">I Laba U</h1>
            <p style="margin: 6px 0 0; color: #64748b; font-size: 13px;">{{ $paymentLabel }}</p>
            <p style="margin: 14px 0 0; color: #0369a1; font-family: monospace; font-size: 15px; font-weight: bold;">{{ $receiptNumber }}</p>
            <p style="margin: 5px 0 0; color: #64748b; font-size: 13px;">Order #{{ $order->order_id }}</p>
        </header>

        <section style="padding: 20px 0; border-bottom: 1px solid #e2e8f0;">
            <h2 style="margin: 0 0 14px; font-size: 16px;">Order details</h2>
            <table role="presentation" style="width: 100%; border-collapse: collapse; font-size: 14px;">
                <tr><td style="padding: 5px 0; color: #64748b;">Customer</td><td style="padding: 5px 0; text-align: right; font-weight: bold;">{{ $order->fullname }}</td></tr>
                <tr><td style="padding: 5px 0; color: #64748b;">Phone</td><td style="padding: 5px 0; text-align: right;">{{ $order->phoneNumber }}</td></tr>
                @if (filled($order->customerEmail))
                    <tr><td style="padding: 5px 0; color: #64748b;">Email</td><td style="padding: 5px 0; text-align: right;">{{ $order->customerEmail }}</td></tr>
                @endif
                <tr><td style="padding: 5px 0; color: #64748b;">Order date</td><td style="padding: 5px 0; text-align: right;">{{ \Illuminate\Support\Carbon::parse($order->order_date)->format('M j, Y') }} · {{ $order->order_time ? \Illuminate\Support\Carbon::parse($order->order_time)->format('g:i A') : 'Time unavailable' }}</td></tr>
                <tr><td style="padding: 5px 0; color: #64748b;">Laundry type</td><td style="padding: 5px 0; text-align: right;">{{ $order->order_type }}</td></tr>
                <tr><td style="padding: 5px 0; color: #64748b;">Quantity</td><td style="padding: 5px 0; text-align: right;">{{ $order->order_type === 'Self Service' ? $order->self_service_loads.' loads' : number_format((float) $order->weight, 2).' kg' }}</td></tr>
                <tr><td style="padding: 5px 0; color: #64748b;">Order status</td><td style="padding: 5px 0; text-align: right;">{{ $order->status }}</td></tr>
                <tr><td style="padding: 5px 0; color: #64748b;">Services</td><td style="padding: 5px 0; text-align: right;">{{ $order->itemDetails ?: $order->services ?: 'Not recorded' }}</td></tr>
                @if (filled($order->specialRequest))
                    <tr><td style="padding: 5px 0; color: #64748b;">Special request</td><td style="padding: 5px 0; text-align: right;">{{ $order->specialRequest }}</td></tr>
                @endif
            </table>
        </section>

        <section style="padding: 20px 0;">
            <h2 style="margin: 0 0 14px; font-size: 16px;">Payment details</h2>
            <table role="presentation" style="width: 100%; border-collapse: collapse; font-size: 14px;">
                <tr><td style="padding: 6px 0; color: #64748b;">Order total</td><td style="padding: 6px 0; text-align: right; font-weight: bold;">₱{{ number_format((float) $order->total_amount, 2) }}</td></tr>
                @if ((float) $order->storage_fee > 0)
                    <tr><td style="padding: 6px 0; color: #64748b;">Storage fee included</td><td style="padding: 6px 0; text-align: right;">₱{{ number_format((float) $order->storage_fee, 2) }}</td></tr>
                @endif
                @if ($previouslyPaid > 0)
                    <tr><td style="padding: 6px 0; color: #64748b;">Paid before this transaction · {{ $order->initialPaymentMethod }}</td><td style="padding: 6px 0; text-align: right;">₱{{ number_format($previouslyPaid, 2) }}</td></tr>
                @endif
                <tr><td style="padding: 8px 0; border-top: 1px solid #bae6fd; font-weight: bold;">This payment · {{ $payment->payment_method }}</td><td style="padding: 8px 0; border-top: 1px solid #bae6fd; text-align: right; font-weight: bold;">₱{{ number_format((float) $payment->amount, 2) }}</td></tr>
                <tr><td style="padding: 6px 0; color: #64748b;">Paid to date</td><td style="padding: 6px 0; text-align: right;">₱{{ number_format((float) $order->amount_paid, 2) }}</td></tr>
                <tr><td style="padding: 6px 0; color: #64748b;">Remaining balance</td><td style="padding: 6px 0; text-align: right;">₱{{ number_format((float) $order->balance, 2) }}</td></tr>
                <tr><td style="padding: 6px 0; color: #64748b;">Payment reference</td><td style="padding: 6px 0; text-align: right; font-weight: bold;">{{ $receiptNumber }}</td></tr>
                <tr><td style="padding: 6px 0; color: #64748b;">Payment date</td><td style="padding: 6px 0; text-align: right;">{{ \Illuminate\Support\Carbon::parse($payment->payment_date)->format('M j, Y') }}</td></tr>
                <tr><td style="padding: 6px 0; color: #64748b;">Payment status</td><td style="padding: 6px 0; text-align: right;">{{ $payment->payment_status }}</td></tr>
                <tr><td style="padding: 6px 0; color: #64748b;">Recorded by</td><td style="padding: 6px 0; text-align: right;">{{ $order->employeeName ?: 'Staff' }}</td></tr>
            </table>
        </section>

        <p style="margin: 0; padding-top: 18px; border-top: 1px dashed #bae6fd; color: #475569; font-size: 13px; text-align: center;">Thank you for choosing I Laba U.</p>
    </main>
</body>
</html>
