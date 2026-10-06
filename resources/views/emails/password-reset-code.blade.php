<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password reset code</title>
</head>
<body style="margin: 0; padding: 24px; background: #f0f8ff; color: #0f172a; font-family: Arial, sans-serif;">
    <main style="max-width: 560px; margin: 0 auto; padding: 28px; border: 1px solid #dbeafe; border-radius: 16px; background: #ffffff; text-align: center;">
        <div style="margin-bottom: 24px; text-align: center;">
            <img src="{{ $message->embed(public_path('images/logo.png')) }}" alt="Ilabau logo" width="180" style="display: block; width: 180px; max-width: 100%; height: auto; margin: 0 auto;">
        </div>
        <h1 style="margin-top: 0; font-size: 24px; text-align: center;">Reset your password</h1>
        <p>Enter this six-digit code on the login page to reset your password:</p>
        <p style="margin: 24px 0; color: #168cff; font-size: 32px; font-weight: 700; letter-spacing: 8px;">{{ $code }}</p>
        <p>This code expires in 10 minutes. If you did not request a password reset, you can ignore this email.</p>
    </main>
</body>
</html>