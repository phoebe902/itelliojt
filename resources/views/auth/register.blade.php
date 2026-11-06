@php
    $field = 'mt-1 w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 placeholder-slate-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20';
    $loginUrl = Route::has('login') ? route('login') : '#';
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign up · IntelliOJT</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=dm-sans:400,500,600,700" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.8/dist/cdn.min.js"></script>
    <style>[x-cloak]{display:none !important}</style>
</head>
<body class="bg-slate-50 font-sans text-slate-900 antialiased">
<div class="grid min-h-screen lg:grid-cols-[minmax(0,5fr)_minmax(0,7fr)]">

    {{-- Brand panel --}}
    <aside class="hidden flex-col justify-between bg-gradient-to-br from-[#10285f] via-[#12388a] to-[#1a4aa0] p-12 text-white lg:flex">
        <div class="flex items-center gap-3">
            <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-600 shadow-lg shadow-blue-900/40">
                <svg class="h-6 w-6 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M4 17l5-5 3 3 6-7" /><path d="M14 8h5v5" />
                </svg>
            </div>
            <div>
                <p class="text-xl font-semibold leading-tight">IntelliOJT</p>
                <p class="text-[11px] text-white/60">Predict. Support. Succeed.</p>
            </div>
        </div>

        <div>
            <h2 class="max-w-md text-4xl font-normal leading-tight">One workspace for the whole OJT program.</h2>
            <p class="mt-4 max-w-md text-sm text-white/70">
                Coordinators, faculty, partner companies and interns work from the same place, from placement to final evaluation.
            </p>

            <ul class="mt-8 space-y-3 text-sm text-white/80">
                @foreach (['Submit requirements and record attendance', 'Spot at-risk interns early', 'Employer evaluations without the paperwork'] as $point)
                    <li class="flex items-center gap-3">
                        <svg class="h-5 w-5 shrink-0 text-teal-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        {{ $point }}
                    </li>
                @endforeach
            </ul>
        </div>

        <p class="text-xs text-white/40">&copy; {{ now()->year }} IntelliOJT</p>
    </aside>

    {{-- Form --}}
    <main class="flex items-start justify-center px-6 py-12 lg:items-center">
        <div class="w-full max-w-2xl"
             x-data="{ role: @js(old('role', 'student')), approval: @js($approvalRoles), showPassword: false }">

            <p class="text-xs font-semibold uppercase tracking-wide text-blue-600">Create account</p>
            <h1 class="mt-2 text-4xl font-normal tracking-tight text-slate-900">Join IntelliOJT</h1>
            <p class="mt-2 text-sm text-slate-500">Choose your role to get started.</p>

            @if ($errors->any())
                <div class="mt-6 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                    Please fix the highlighted fields and try again.
                </div>
            @endif

            <form method="POST" action="{{ route('register.store') }}" class="mt-8 space-y-6">
                @csrf

                {{-- Role picker --}}
                <fieldset>
                    <legend class="text-xs font-semibold text-slate-500">I am a…</legend>

                    <div class="mt-2 grid grid-cols-1 gap-3 sm:grid-cols-2">
                        @foreach ($roles as $role)
                            <label class="cursor-pointer">
                                <input type="radio" name="role" value="{{ $role->value }}" x-model="role" class="peer sr-only">
                                <div class="flex h-full gap-3 rounded-xl border border-slate-200 bg-white p-4 transition hover:border-slate-300
                                            peer-checked:border-blue-600 peer-checked:bg-blue-50/60 peer-checked:ring-2 peer-checked:ring-blue-600/20
                                            peer-focus-visible:ring-2 peer-focus-visible:ring-blue-600">
                                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-blue-50 text-blue-600">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="{{ $role->icon() }}" />
                                        </svg>
                                    </span>
                                    <span>
                                        <span class="block text-sm font-semibold text-slate-900">{{ $role->label() }}</span>
                                        <span class="mt-0.5 block text-[11px] leading-snug text-slate-500">{{ $role->description() }}</span>
                                    </span>
                                </div>
                            </label>
                        @endforeach
                    </div>
                    @error('role') <p class="mt-1 text-xs text-rose-600">{{ $message }}</p> @enderror

                    <p x-cloak x-show="approval.includes(role)"
                       class="mt-3 rounded-lg border border-amber-200 bg-amber-50 px-3 py-2 text-xs text-amber-800">
                        This role needs administrator approval before you can sign in.
                    </p>
                </fieldset>

                {{-- Account details --}}
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <label class="block">
                        <span class="text-xs font-semibold text-slate-500">Username</span>
                        <input name="username" value="{{ old('username') }}" autocomplete="username" required class="{{ $field }}">
                        @error('username') <span class="mt-1 block text-xs text-rose-600">{{ $message }}</span> @enderror
                    </label>

                    <label class="block">
                        <span class="text-xs font-semibold text-slate-500">Email</span>
                        <input type="email" name="email" value="{{ old('email') }}" autocomplete="email" required class="{{ $field }}">
                        @error('email') <span class="mt-1 block text-xs text-rose-600">{{ $message }}</span> @enderror
                    </label>
                </div>

                {{-- Role-specific fields --}}
                <div x-cloak x-show="role === 'student'">
                    <label class="block">
                        <span class="text-xs font-semibold text-slate-500">Student number</span>
                        <input name="student_number" value="{{ old('student_number') }}" placeholder="e.g. BSIT-2025-0142" class="{{ $field }}">
                        @error('student_number') <span class="mt-1 block text-xs text-rose-600">{{ $message }}</span> @enderror
                    </label>
                </div>

                <div x-cloak x-show="role === 'company_supervisor'">
                    <label class="block">
                        <span class="text-xs font-semibold text-slate-500">Company</span>
                        <select name="company_id" class="{{ $field }}">
                            <option value="">Select your company</option>
                            @foreach ($companies as $company)
                                <option value="{{ $company->id }}" @selected((string) old('company_id') === (string) $company->id)>{{ $company->name }}</option>
                            @endforeach
                        </select>
                        @error('company_id') <span class="mt-1 block text-xs text-rose-600">{{ $message }}</span> @enderror
                    </label>
                </div>

                <div x-cloak x-show="role === 'faculty_supervisor'">
                    <label class="block">
                        <span class="text-xs font-semibold text-slate-500">Department</span>
                        <input name="department" value="{{ old('department') }}" placeholder="e.g. College of Computer Studies" class="{{ $field }}">
                        @error('department') <span class="mt-1 block text-xs text-rose-600">{{ $message }}</span> @enderror
                    </label>
                </div>

                {{-- Password --}}
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <label class="block">
                        <span class="flex items-center justify-between text-xs font-semibold text-slate-500">
                            Password
                            <button type="button" @click="showPassword = !showPassword" class="font-medium text-blue-600 hover:text-blue-700"
                                    x-text="showPassword ? 'Hide' : 'Show'">Show</button>
                        </span>
                        <input :type="showPassword ? 'text' : 'password'" name="password" autocomplete="new-password" required class="{{ $field }}">
                        @error('password') <span class="mt-1 block text-xs text-rose-600">{{ $message }}</span> @enderror
                    </label>

                    <label class="block">
                        <span class="text-xs font-semibold text-slate-500">Confirm password</span>
                        <input :type="showPassword ? 'text' : 'password'" name="password_confirmation" autocomplete="new-password" required class="{{ $field }}">
                    </label>
                </div>

                <button class="w-full rounded-xl bg-blue-600 px-5 py-3.5 text-sm font-semibold text-white shadow-lg shadow-blue-600/25 transition hover:bg-blue-700"
                        x-text="approval.includes(role) ? 'Request account' : 'Create account'">
                    Create account
                </button>

                <p class="text-center text-sm text-slate-500">
                    Already have an account?
                    <a href="{{ $loginUrl }}" class="font-semibold text-blue-600 hover:text-blue-700">Sign in</a>
                </p>
            </form>
        </div>
    </main>
</div>
</body>
</html>