<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ready for Pickup - Order #{{ $orderId }}</title>
</head>
<body style="margin: 0; padding: 24px; background: #f0f8ff; color: #0f172a; font-family: Arial, sans-serif;">
    <main style="max-width: 600px; margin: 0 auto; padding: 28px; border: 1px solid #dbeafe; border-radius: 16px; background: #ffffff;">
        <header style="padding-bottom: 20px; border-bottom: 1px dashed #bae6fd; text-align: center;">
            <img src="{{ $message->embed(public_path('images/logo.png')) }}" alt="I Laba U" width="80" style="display: block; width: 80px; height: auto; margin: 0 auto 14px;">
            <h1 style="margin: 0; color: #0f172a; font-size: 22px; font-weight: bold; letter-spacing: 0.04em;">READY FOR PICKUP</h1>
        </header>
        <section style="padding: 24px 0;">
            <p style="margin: 0 0 14px; color: #0f172a; font-size: 15px; line-height: 1.6;">Dear {{ $customerName }},</p>
            <p style="margin: 0; color: #475569; font-size: 14px; line-height: 1.7;">We are pleased to inform you that your laundry order <strong>#{{ $orderId }}</strong> is ready for pickup. Please visit I Laba U during business hours to collect your items.</p>
            @if ($remainingBalance > 0)
                <p style="margin: 16px 0 0; color: #475569; font-size: 14px; line-height: 1.7;">An outstanding balance of <strong>&#8369;{{ number_format($remainingBalance, 2) }}</strong> remains on your order. Please settle this balance when collecting your items.</p>
            @endif
            <p style="margin: 18px 0 0; color: #475569; font-size: 14px; line-height: 1.7;">Thank you for choosing I Laba U. We look forward to serving you again.</p>
        </section>
    </main>
</body>
</html>