@php
    $statusStyles = [
        'pending'   => 'bg-amber-50 text-amber-700',
        'active'    => 'bg-emerald-50 text-emerald-700',
        'suspended' => 'bg-rose-50 text-rose-700',
    ];

    $tabs = ['' => 'All users', 'pending' => 'Pending approval', 'active' => 'Active', 'suspended' => 'Suspended'];

    $tabUrl = fn (?string $s) => route('admin.users.index', [
        ...request()->except(['status', 'page']),
        ...($s ? ['status' => $s] : []),
    ]);

    $field = 'mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20';
    $total = (int) $counts->sum();
@endphp

<x-layouts.app title="Users">
    <div x-data="{ open: @js($errors->createUser->any()), role: @js(old('role', 'student')) }">

        {{-- Header --}}
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-blue-600">Administration</p>
                <h1 class="mt-2 text-4xl font-normal tracking-tight text-slate-900">Users</h1>
                <p class="mt-2 text-sm text-slate-500">Approve new accounts, assign roles, and manage who can access IntelliOJT.</p>
            </div>

            <button type="button" @click="open = true"
                    class="rounded-xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-blue-600/25 transition hover:bg-blue-700">
                + Add user
            </button>
        </div>

        @if (session('status'))
            <div class="mt-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('status') }}</div>
        @endif
        @if (session('error') || $errors->default->any())
            <div class="mt-6 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">{{ session('error') ?? $errors->default->first() }}</div>
        @endif

        {{-- Summary --}}
        <div class="mt-8 grid grid-cols-2 divide-x divide-slate-200 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm lg:grid-cols-4">
            @foreach ([
                ['Total users',       $total,                       'bg-blue-600'],
                ['Pending approval',  $counts['pending'] ?? 0,      'bg-amber-400'],
                ['Active',            $counts['active'] ?? 0,       'bg-emerald-500'],
                ['Suspended',         $counts['suspended'] ?? 0,    'bg-rose-500'],
            ] as [$label, $value, $dot])
                <div class="flex items-start gap-3 p-6">
                    <span class="mt-2 h-2.5 w-2.5 rounded-full {{ $dot }}"></span>
                    <div>
                        <p class="text-2xl font-semibold text-slate-900">{{ $value }}</p>
                        <p class="text-xs text-slate-400">{{ $label }}</p>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Table card --}}
        <section class="mt-6 rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-3 p-5">
                <nav class="flex flex-wrap gap-1">
                    @foreach ($tabs as $value => $label)
                        <a href="{{ $tabUrl($value ?: null) }}"
                           @class([
                               'rounded-lg px-4 py-2 text-xs font-semibold transition',
                               'bg-blue-50 text-blue-700' => ($status ?? '') === $value,
                               'text-slate-500 hover:bg-slate-50 hover:text-slate-800' => ($status ?? '') !== $value,
                           ])>{{ $label }}</a>
                    @endforeach
                </nav>

                <form method="GET" action="{{ route('admin.users.index') }}" class="flex items-center gap-2">
                    @if ($status) <input type="hidden" name="status" value="{{ $status }}"> @endif
                    <input type="search" name="search" value="{{ $search }}" placeholder="Search name, username, email"
                           class="w-60 rounded-xl border border-slate-200 bg-slate-50/50 px-4 py-2.5 text-sm focus:border-blue-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                    <select name="role" onchange="this.form.submit()" class="rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-600">
                        <option value="">All roles</option>
                        @foreach ($roles as $r)
                            <option value="{{ $r->value }}" @selected($role === $r)>{{ $r->label() }}</option>
                        @endforeach
                    </select>
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="border-y border-slate-200 bg-slate-50 text-[11px] font-semibold uppercase tracking-wider text-slate-400">
                        <tr>
                            <th class="px-6 py-4">User</th>
                            <th class="px-6 py-4">Role</th>
                            <th class="px-6 py-4">Details</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4">Registered</th>
                            <th class="px-6 py-4 text-right">Actions</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">
                        @forelse ($users as $u)
                            @php $isSelf = $u->is(auth()->user()); @endphp
                            <tr class="transition hover:bg-slate-50/70">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-4">
                                        <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-xs font-semibold text-blue-600">{{ $u->initials }}</span>
                                        <div class="min-w-0">
                                            <p class="truncate text-sm font-semibold text-slate-900">
                                                {{ $u->full_name }} @if ($isSelf)<span class="ml-1 text-[10px] font-medium text-slate-400">(you)</span>@endif
                                            </p>
                                            <p class="truncate text-[11px] text-slate-400">{{ $u->email }} · &#64;{{ $u->username }}</p>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-6 py-4">
                                    <form method="POST" action="{{ route('admin.users.update', $u) }}">
                                        @csrf @method('PATCH')
                                        <select name="role" onchange="this.form.submit()" @disabled($isSelf)
                                                class="rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-xs text-slate-700 disabled:bg-slate-50 disabled:text-slate-400">
                                            @foreach ($roles as $r)
                                                <option value="{{ $r->value }}" @selected($u->role === $r)>{{ $r->label() }}</option>
                                            @endforeach
                                        </select>
                                    </form>
                                </td>

                                <td class="px-6 py-4 text-xs text-slate-500">
                                    {{ $u->student_number ?? $u->company?->name ?? $u->department ?? '—' }}
                                </td>

                                <td class="px-6 py-4">
                                    <span class="inline-block rounded-full px-3 py-1 text-[11px] font-semibold capitalize {{ $statusStyles[$u->status] ?? 'bg-slate-100 text-slate-600' }}">
                                        {{ $u->status }}
                                    </span>
                                </td>

                                <td class="px-6 py-4 text-xs text-slate-500">{{ $u->created_at?->format('M j, Y') }}</td>

                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-end gap-2">
                                        @unless ($isSelf)
                                            @if ($u->status === 'pending')
                                                <form method="POST" action="{{ route('admin.users.update', $u) }}">
                                                    @csrf @method('PATCH')
                                                    <input type="hidden" name="status" value="active">
                                                    <button class="rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-emerald-700">Approve</button>
                                                </form>
                                            @elseif ($u->status === 'active')
                                                <form method="POST" action="{{ route('admin.users.update', $u) }}">
                                                    @csrf @method('PATCH')
                                                    <input type="hidden" name="status" value="suspended">
                                                    <button class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-600 hover:bg-slate-50">Suspend</button>
                                                </form>
                                            @else
                                                <form method="POST" action="{{ route('admin.users.update', $u) }}">
                                                    @csrf @method('PATCH')
                                                    <input type="hidden" name="status" value="active">
                                                    <button class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-600 hover:bg-slate-50">Reactivate</button>
                                                </form>
                                            @endif

                                            <form method="POST" action="{{ route('admin.users.destroy', $u) }}"
                                                  onsubmit="return confirm('Delete {{ addslashes($u->full_name) }}? This cannot be undone.')">
                                                @csrf @method('DELETE')
                                                <button class="rounded-lg p-2 text-slate-400 hover:bg-rose-50 hover:text-rose-600" aria-label="Delete {{ $u->full_name }}">
                                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                                        <path d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                                    </svg>
                                                </button>
                                            </form>
                                        @endunless
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-6 py-16 text-center text-sm text-slate-400">No users match your filters.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($users->hasPages())
                <div class="border-t border-slate-100 px-6 py-4">{{ $users->links() }}</div>
            @endif
        </section>

        {{-- Add user modal (Alpine.js) --}}
        <div x-cloak x-show="open" x-transition.opacity @keydown.escape.window="open = false"
             class="fixed inset-0 z-40 flex items-center justify-center bg-slate-900/40 p-4">
            <div @click.outside="open = false" class="max-h-[90vh] w-full max-w-lg overflow-y-auto rounded-2xl bg-white p-6 shadow-xl">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-slate-900">Add user</h2>
                    <button type="button" @click="open = false" class="text-slate-400 hover:text-slate-600" aria-label="Close">✕</button>
                </div>

                <form method="POST" action="{{ route('admin.users.store') }}" class="mt-5 space-y-4">
                    @csrf

                    <div class="grid grid-cols-2 gap-4">
                        <label class="block">
                            <span class="text-xs font-semibold text-slate-500">First name</span>
                            <input name="first_name" value="{{ old('first_name') }}" required class="{{ $field }}">
                            @error('first_name', 'createUser') <span class="text-xs text-rose-600">{{ $message }}</span> @enderror
                        </label>
                        <label class="block">
                            <span class="text-xs font-semibold text-slate-500">Last name</span>
                            <input name="last_name" value="{{ old('last_name') }}" required class="{{ $field }}">
                            @error('last_name', 'createUser') <span class="text-xs text-rose-600">{{ $message }}</span> @enderror
                        </label>
                    </div>

                    <label class="block">
                        <span class="text-xs font-semibold text-slate-500">Email address</span>
                        <input type="email" name="email" value="{{ old('email') }}" required class="{{ $field }}">
                        @error('email', 'createUser') <span class="text-xs text-rose-600">{{ $message }}</span> @enderror
                    </label>

                    <label class="block">
                        <span class="text-xs font-semibold text-slate-500">User type</span>
                        <select name="role" x-model="role" class="{{ $field }}">
                            @foreach ($roles as $r)
                                <option value="{{ $r->value }}">{{ $r->label() }}</option>
                            @endforeach
                        </select>
                        @error('role', 'createUser') <span class="text-xs text-rose-600">{{ $message }}</span> @enderror
                    </label>

                    <label class="block" x-cloak x-show="role === 'student'">
                        <span class="text-xs font-semibold text-slate-500">Student number (optional)</span>
                        <input name="student_number" value="{{ old('student_number') }}" class="{{ $field }}">
                        @error('student_number', 'createUser') <span class="text-xs text-rose-600">{{ $message }}</span> @enderror
                    </label>

                    <label class="block" x-cloak x-show="role === 'company_supervisor'">
                        <span class="text-xs font-semibold text-slate-500">Company (optional)</span>
                        <select name="company_id" class="{{ $field }}">
                            <option value="">— None —</option>
                            @foreach ($companies as $company)
                                <option value="{{ $company->id }}" @selected((string) old('company_id') === (string) $company->id)>{{ $company->name }}</option>
                            @endforeach
                        </select>
                    </label>

                    <label class="block" x-cloak x-show="role === 'faculty_supervisor'">
                        <span class="text-xs font-semibold text-slate-500">Department (optional)</span>
                        <input name="department" value="{{ old('department') }}" class="{{ $field }}">
                    </label>

                    <label class="block">
                        <span class="text-xs font-semibold text-slate-500">Temporary password</span>
                        <input type="text" name="password" required autocomplete="off" class="{{ $field }}">
                        <span class="mt-1 block text-[11px] text-slate-400">Share this with the user. At least 8 characters.</span>
                        @error('password', 'createUser') <span class="text-xs text-rose-600">{{ $message }}</span> @enderror
                    </label>

                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" @click="open = false" class="rounded-lg px-4 py-2 text-sm font-medium text-slate-500 hover:text-slate-800">Cancel</button>
                        <button class="rounded-lg bg-blue-600 px-5 py-2 text-sm font-semibold text-white hover:bg-blue-700">Create user</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>