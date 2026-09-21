@props([
    'navigation' => true,
    'fullWidth' => false,
])

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <title>{{ $title ?? 'I Laba U' }}</title>
</head>

<body @class([
    'min-h-screen font-sans text-slate-900 antialiased',
    'bg-[#eef2fb]' => $fullWidth,
    'bg-[#fff4f8]' => ! $fullWidth && auth()->check() && auth()->user()->role === 'employee',
    'bg-[#f7f6f3]' => ! $fullWidth && (! auth()->check() || auth()->user()->role !== 'employee'),
])>
    @if ($navigation)
        @if (auth()->check() && auth()->user()->role === 'employee')
            <x-employee-nav />
        @else
            <x-nav />
        @endif
    @endif

    <main class="{{ $fullWidth ? '' : 'mx-auto max-w-[1178px] px-4 py-6 sm:px-6' }}">
        {{ $slot }}
    </main>

</body>

</html>
