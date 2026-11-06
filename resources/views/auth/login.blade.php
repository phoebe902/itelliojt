@php
    $roles = $roles ?? \App\Enums\UserRole::cases();

    $input       = 'w-full rounded-xl border border-slate-200 bg-slate-50/60 px-4 py-3.5 text-sm text-slate-800 placeholder-slate-400 transition focus:border-blue-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20';
    $label       = 'mb-1.5 block text-xs font-semibold text-slate-700';
    $registerUrl = Route::has('register') ? route('register') : '#';
    $forgotUrl   = Route::has('password.request') ? route('password.request') : '#';
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign in · IntelliOJT</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=dm-sans:400,500,600,700" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>
    <style>[x-cloak]{display:none !important}</style>
</head>
<body class="bg-white font-sans text-slate-900 antialiased">
<div class="grid min-h-screen lg:grid-cols-2">

    <x-auth.brand-panel />

    {{-- ============ Form panel ============ --}}
    <main class="flex min-h-screen flex-col px-6 sm:px-12">
        <div class="mx-auto flex w-full max-w-[30rem] flex-1 items-center py-12"
             x-data="{
                 role: @js(old('role', 'student')),
                 descriptions: @js(collect($roles)->mapWithKeys(fn ($r) => [$r->value => $r->description()])),
                 showPassword: false
             }">
            <div class="w-full">
                <p class="text-[11px] font-semibold uppercase tracking-[0.14em] text-blue-600">Sign in</p>
                <h1 class="mt-3 text-[40px] font-normal leading-tight tracking-tight text-slate-900">Welcome back</h1>
                <p class="mt-3 text-sm text-slate-400">Choose your user type and sign in to your OJT workspace.</p>

                @if (session('status'))
                    <div class="mt-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login.store') }}" class="mt-9 space-y-5">
                    @csrf

                    {{-- Role picker --}}
                    <fieldset>
                        <legend class="{{ $label }}">Sign in as</legend>

                        <div class="mt-2 flex flex-wrap gap-2">
                            @foreach ($roles as $role)
                                <label class="cursor-pointer">
                                    <input type="radio" name="role" value="{{ $role->value }}" x-model="role" class="peer sr-only">
                                    <span class="inline-block rounded-full border border-slate-200 bg-white px-4 py-2 text-xs font-semibold text-slate-600 transition
                                                 hover:border-slate-300
                                                 peer-checked:border-blue-600 peer-checked:bg-blue-600 peer-checked:text-white
                                                 peer-focus-visible:ring-2 peer-focus-visible:ring-blue-600 peer-focus-visible:ring-offset-2">
                                        {{ $role->label() }}
                                    </span>
                                </label>
                            @endforeach
                        </div>

                        <p class="mt-2.5 min-h-[2rem] text-[11px] leading-snug text-slate-400" x-text="descriptions[role]"></p>
                        @error('role') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </fieldset>

                    {{-- Username or email --}}
                    <div>
                        <label for="login" class="{{ $label }}">Username or email</label>
                        <input id="login" name="login" value="{{ old('login') }}" autocomplete="username" required autofocus class="{{ $input }}">
                        @error('login') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>

                    {{-- Password --}}
                    <div>
                        <label for="password" class="{{ $label }} flex items-center justify-between">
                            Password
                            <button type="button" @click="showPassword = !showPassword"
                                    class="text-[11px] font-medium text-blue-600 hover:text-blue-700" x-text="showPassword ? 'Hide' : 'Show'">Show</button>
                        </label>
                        <input id="password" :type="showPassword ? 'text' : 'password'" name="password" autocomplete="current-password" required class="{{ $input }}">
                        @error('password') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex items-center justify-between">
                        <label class="flex cursor-pointer items-center gap-2 text-xs text-slate-500">
                            <input type="checkbox" name="remember" value="1" class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                            Keep me signed in
                        </label>
                        <a href="{{ $forgotUrl }}" class="text-xs font-semibold text-blue-600 hover:text-blue-700">Forgot password?</a>
                    </div>

                    <button class="flex w-full items-center justify-center gap-3 rounded-xl bg-gradient-to-r from-blue-600 to-blue-700 px-5 py-4 text-sm font-semibold text-white shadow-lg shadow-blue-600/30 transition hover:from-blue-700 hover:to-blue-800">
                        Sign in
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6" /></svg>
                    </button>

                    <p class="pt-2 text-center text-xs text-slate-400">
                        New to IntelliOJT?
                        <a href="{{ $registerUrl }}" class="font-semibold text-blue-600 hover:text-blue-700">Create an account</a>
                    </p>
                </form>
            </div>
        </div>

        <footer class="flex items-center justify-center gap-3 pb-6 text-[10px] text-slate-400">
            <a href="#" class="hover:text-slate-600">Privacy</a><span>&middot;</span>
            <a href="#" class="hover:text-slate-600">Terms of use</a><span>&middot;</span>
            <a href="#" class="hover:text-slate-600">Help center</a>
        </footer>
    </main>
</div>
</body>
</html>