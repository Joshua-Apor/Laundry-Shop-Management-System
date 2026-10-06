<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to the shop</title>
</head>
<body style="margin: 0; padding: 24px; background: #f0f8ff; color: #0f172a; font-family: Arial, sans-serif;">
    <main style="max-width: 560px; margin: 0 auto; padding: 28px; border: 1px solid #dbeafe; border-radius: 16px; background: #ffffff;">
        <div style="margin-bottom: 24px; text-align: center;">
            <img src="{{ $message->embed(public_path('images/logo.png')) }}" alt="Ilabau logo" width="180" style="display: block; width: 180px; max-width: 100%; height: auto; margin: 0 auto;">
        </div>
        <h1 style="margin-top: 0; font-size: 24px; text-align: center;">Welcome to the shop, {{ $employeeName }}!</h1>
        <p>Your employee account has been created. Use these details to sign in:</p>
        <p><strong>Username:</strong> {{ $username }}</p>
        <p><strong>Temporary password:</strong> {{ $password }}</p>
        <p>Please sign in and change your password as soon as possible.</p>
    </main>
</body>
</html>