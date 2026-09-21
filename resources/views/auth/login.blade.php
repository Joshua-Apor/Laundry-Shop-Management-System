<x-layout title="I Laba U" :navigation="false" :full-width="true">

    <div class="grid min-h-screen lg:grid-cols-[1fr_1fr]">

        <!-- LEFT SIDE -->
        <section
            class="relative flex min-h-[560px] flex-col overflow-hidden bg-[linear-gradient(145deg,#e51f87_0%,#b247b1_44%,#39b9ed_100%)] px-8 py-10 text-white sm:px-12 lg:min-h-screen lg:px-10 xl:px-14">

            <!-- Logo / Branding -->
            <div class="relative z-10 flex items-center gap-4">

                <img
                    src="{{ asset('images/logo.png') }}"
                    alt="Laba U logo"
                    class="h-14 w-14 rounded-xl object-cover shadow-sm ring-1 ring-white/40"
                >

                <div class="flex flex-col">

                    <span class="text-2xl font-extrabold tracking-tight">
                        I Laba U
                    </span>

                    <span class="mt-0.5 text-sm font-medium text-white/75">
                        Laundry Shop Management System
                    </span>

                </div>
            </div>

            <!-- Main Description -->
            <div class="relative z-10 my-auto max-w-xl py-16">

                <h1
                    class="max-w-md text-4xl font-extrabold leading-[1.08] tracking-[-0.04em] sm:text-5xl">
                    Manage your shop,<br>
                    effortlessly.
                </h1>

                <p class="mt-5 max-w-xl text-sm leading-6 text-white/80">
                    Track laundry orders, manage customers, record payments, and notify
                    customers when their laundry is ready — all from one place.
                </p>

                <!-- Features -->
                <div class="mt-8 grid max-w-lg grid-cols-1 gap-5 text-sm font-medium sm:grid-cols-2">

                    <span class="flex items-center gap-3">
                        <i class="fa-solid fa-basket-shopping w-5 text-center text-lg"
                            aria-hidden="true"></i>
                        Order Tracking
                    </span>

                    <span class="flex items-center gap-3">
                        <i class="fa-solid fa-comment-sms w-5 text-center text-lg"
                            aria-hidden="true"></i>
                        SMS Notifications
                    </span>

                    <span class="flex items-center gap-3">
                        <i class="fa-solid fa-credit-card w-5 text-center text-lg"
                            aria-hidden="true"></i>
                        Payment Monitoring
                    </span>

                    <span class="flex items-center gap-3">
                        <i class="fa-solid fa-chart-column w-5 text-center text-lg"
                            aria-hidden="true"></i>
                        Sales Reports
                    </span>

                </div>
            </div>

            <!-- Footer -->
            <p class="relative z-10 text-xs text-white/60">
                Purok Narra, Tagum City — © 2026
            </p>

            <!-- Background Decoration -->
            <span
                class="absolute -right-24 top-1/2 h-72 w-72 rounded-full bg-white/10 blur-3xl">
            </span>

        </section>


        <!-- RIGHT SIDE -->
        <section class="flex items-center justify-center px-6 py-12 sm:px-12 lg:px-16 xl:px-24">

            <div class="w-full max-w-[390px]">

                <!-- Heading -->
                <h2 class="text-2xl font-extrabold tracking-[-0.03em]">
                    Welcome back
                </h2>

                <p class="mt-1 text-sm text-[#66809b]">
                    Sign in to access the staff portal
                </p>


                <!-- Login Form -->
                <form
                    method="POST"
                    action="{{ route('login.store') }}"
                    class="mt-7 space-y-4"
                    autocomplete="off"
                >

                    @csrf


                    <!-- Role -->
                    <fieldset>

                        <legend class="mb-2 text-[11px] font-bold uppercase tracking-wide text-[#66809b]">
                            Sign in as
                        </legend>

                        <div class="grid grid-cols-2 gap-1 rounded-xl border border-[#b9dff3] bg-[#e8f5fb] p-1">

                            @foreach (['employee' => 'Employee', 'manager' => 'Manager'] as $value => $label)

                                <label
                                    class="cursor-pointer rounded-lg px-3 py-2.5 text-center text-xs font-semibold transition has-[:checked]:bg-white has-[:checked]:text-[#e92c81] has-[:checked]:shadow-sm"
                                >

                                    <input
                                        type="radio"
                                        name="role"
                                        value="{{ $value }}"
                                        class="sr-only"
                                        @checked(old('role', 'employee') === $value)
                                    >

                                    <span>
                                        <i
                                            class="fa-solid {{ $value === 'employee' ? 'fa-user' : 'fa-tag' }} mr-1"
                                            aria-hidden="true">
                                        </i>

                                        {{ $label }}
                                    </span>

                                </label>

                            @endforeach

                        </div>

                    </fieldset>


                    <!-- Username -->
                    <label
                        class="block text-sm font-semibold"
                        for="username"
                    >
                        Username

                        <input
                            id="username"
                            name="username"
                            required
                            autofocus
                            placeholder="e.g. josh"
                            autocomplete="off"
                            spellcheck="false"
                            class="mt-1.5 block w-full rounded-xl border border-[#b9dff3] bg-white px-3.5 py-3 text-sm font-normal outline-none transition placeholder:text-[#a3aeba] focus:border-[#4bb8eb] focus:ring-4 focus:ring-[#4bb8eb]/15"
                        >

                    </label>


                    <!-- Password -->
                    <label
                        class="block text-sm font-semibold"
                        for="password"
                    >
                        Password

                        <span class="relative mt-1.5 block">

                            <input
                                id="password"
                                type="password"
                                name="password"
                                required
                                placeholder="••••••••"
                                autocomplete="new-password"
                                class="block w-full rounded-xl border border-[#b9dff3] bg-white px-3.5 py-3 pr-11 text-sm font-normal outline-none transition placeholder:text-[#a3aeba] focus:border-[#4bb8eb] focus:ring-4 focus:ring-[#4bb8eb]/15"
                            >

                            <button
                                type="button"
                                aria-label="Show password"
                                data-password-toggle
                                class="absolute inset-y-0 right-0 px-3 text-sm text-[#66809b]"
                            >
                                <i class="fa-solid fa-eye" aria-hidden="true"></i>
                            </button>

                        </span>

                    </label>


                    <!-- Error -->
                    @error('username')

                        <p class="text-xs font-medium text-rose-600">
                            {{ $message }}
                        </p>

                    @enderror


                    <!-- Sign In Button -->
                    <button
                        type="submit"
                        class="w-full rounded-xl bg-[#168cff] px-4 py-3 text-sm font-extrabold text-white shadow-lg shadow-[#168cff]/25 transition hover:-translate-y-0.5 hover:bg-[#0878df] focus:outline-none focus:ring-4 focus:ring-[#168cff]/20"
                    >
                        Sign In
                    </button>

                </form>


                <!-- Demo Credentials -->
                <div class="mt-5 rounded-xl bg-[#e5f3fa] px-3.5 py-3 text-xs leading-5 text-[#66809b]">

                    <strong class="block text-[#56829e]">
                        Demo credentials
                    </strong>

                    <span>
                        <i class="fa-solid fa-user mr-1" aria-hidden="true"></i>
                        Employee — josh / employee123
                    </span>

                    <br>

                    <span>
                        <i class="fa-solid fa-tag mr-1" aria-hidden="true"></i>
                        Manager — manager / manager123
                    </span>

                </div>

            </div>

        </section>

    </div>


    <!-- Password Toggle -->
    <script>

        document
            .querySelector('[data-password-toggle]')
            .addEventListener('click', (event) => {

                const password = document.querySelector('#password');
                const icon = event.currentTarget.querySelector('i');

                const isPassword = password.type === 'password';

                password.type = isPassword ? 'text' : 'password';

                event.currentTarget.setAttribute(
                    'aria-label',
                    isPassword ? 'Hide password' : 'Show password'
                );

                icon.classList.toggle('fa-eye', !isPassword);
                icon.classList.toggle('fa-eye-slash', isPassword);

            });

    </script>

</x-layout>