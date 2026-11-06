
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Account') | IntelliOJT</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-white font-sans text-slate-900 antialiased">

    <main class="grid min-h-screen lg:grid-cols-2">

        <!-- Promotional panel -->
        <section class="relative hidden overflow-hidden bg-gradient-to-br from-slate-950 via-blue-950 to-indigo-900 p-12 text-white lg:flex lg:flex-col lg:justify-between xl:p-16">

            <div class="pointer-events-none absolute -right-32 top-16 h-96 w-96 rounded-full bg-blue-500/20 blur-3xl"></div>
            <div class="pointer-events-none absolute -bottom-40 -left-20 h-96 w-96 rounded-full bg-indigo-500/20 blur-3xl"></div>

            <a href="{{ route('login') }}" class="relative flex items-center gap-3 font-semibold tracking-wide">
                <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-white/10 ring-1 ring-white/20">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-6 w-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3 19 6v5c0 4.5-3 7.5-7 10-4-2.5-7-5.5-7-10V6l7-3Z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="m9 12 2 2 4-4"/>
                    </svg>
                </span>
                <span class="text-lg">IntelliOJT</span>
            </a>

            <div class="relative max-w-xl py-16">
            

                <h1 class="text-4xl font-semibold leading-tight tracking-tight xl:text-5xl">
                    A better way to manage your work.
                </h1>

                <p class="mt-6 max-w-md text-lg leading-8 text-blue-100/75">
                    Access your account, manage your activities, and keep everything organized in one place.
                </p>

                
            </div>

          
        </section>

        <!-- Login or registration content -->
        <section class="flex min-h-screen items-center justify-center px-6 py-12 sm:px-10 lg:px-12 xl:px-20">
            <div class="w-full max-w-md">

                <a href="{{ route('login') }}" class="mb-12 flex items-center gap-3 font-semibold text-slate-900 lg:hidden">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-600 text-white">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="h-6 w-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3 19 6v5c0 4.5-3 7.5-7 10-4-2.5-7-5.5-7-10V6l7-3Z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="m9 12 2 2 4-4"/>
                        </svg>
                    </span>
                    YourApp
                </a>

                @yield('content')

                <p class="mt-10 text-center text-xs leading-5 text-slate-400">
                    By continuing, you agree to use this service responsibly.
                </p>
            </div>
        </section>
    </main>

</body>
</html>
