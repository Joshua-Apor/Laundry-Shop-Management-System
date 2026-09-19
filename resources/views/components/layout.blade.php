<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <title>{{ $title ?? 'Laba U Staff Portal' }}</title>
</head>

<body class="min-h-screen bg-[#f7f6f3] font-sans text-slate-900 antialiased">
    
    <x-nav />

    <main class="mx-auto max-w-[1178px] px-4 py-6 sm:px-6">
        {{ $slot }}
    </main>

</body>

</html>
