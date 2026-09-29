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

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
    >

    <link
        rel="icon"
        type="image/png"
        href="{{ asset('images/logo.png') }}"
    >

    <title>{{ $title ?? 'I Laba U' }}</title>

</head>


<body class="min-h-screen bg-[#F0F8FF] font-sans text-slate-900 antialiased">

    @if (session('success'))

        <div
            data-dropdown-message
            data-dropdown-message-duration="5000"
            role="status"
            aria-live="polite"
            aria-atomic="true"
            class="invisible pointer-events-none fixed left-1/2 top-4 z-[100] flex w-[min(22rem,calc(100vw-2rem))] -translate-x-1/2 -translate-y-2 items-start gap-3 rounded-xl border border-emerald-200 bg-white p-4 text-slate-900 opacity-0 shadow-xl transition duration-300 ease-out"
        >

            <span class="grid size-9 shrink-0 place-items-center rounded-full bg-emerald-100 text-emerald-700">
                <i class="fa-solid fa-check" aria-hidden="true"></i>
            </span>

            <div class="min-w-0 flex-1">

                <p class="text-sm font-semibold">
                    Success
                </p>

                <p
                    data-dropdown-message-content
                    class="mt-0.5 text-sm leading-5 text-slate-600"
                >
                    {{ session('success') }}
                </p>

            </div>

            <button
                type="button"
                data-dropdown-message-dismiss
                aria-label="Dismiss notification"
                class="-mr-1 -mt-1 grid size-8 shrink-0 place-items-center rounded-md text-slate-500 transition hover:bg-slate-100 hover:text-slate-800 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-600"
            >
                <i class="fa-solid fa-xmark" aria-hidden="true"></i>
            </button>

        </div>

    @endif

    @if ($navigation)
        @if (auth()->check() && auth()->user()->role === 'employee')
            <x-employee-nav />
        @elseif (auth()->check() && auth()->user()->role === 'manager')
            <x-manager-nav />
        @else
            @includeIf('components.nav')
        @endif
    @endif


    <main class="{{ $fullWidth ? '' : 'mx-auto max-w-[1178px] px-4 py-6 sm:px-6' }}">
        {{ $slot }}
    </main>

    <script src="{{ asset('js/loadinganimation.js') }}"></script>
    <script src="{{ asset('js/passwordtoggle.js') }}"></script>
    <script src="{{ asset('js/dropdownmessage.js') }}"></script>

</body>

</html>