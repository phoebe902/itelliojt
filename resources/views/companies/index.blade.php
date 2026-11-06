
@php
$isAdmin = auth()->user()?->role === \App\Enums\UserRole::Administrator;
    // Deterministic tile colour per company (no colour column needed)
    $tones = [
        'bg-blue-50 text-blue-700',
        'bg-violet-50 text-violet-700',
        'bg-emerald-50 text-emerald-700',
        'bg-amber-50 text-amber-700',
        'bg-rose-50 text-rose-700',
        'bg-sky-50 text-sky-700',
    ];

    $filterUrl = fn (?string $value) => route('companies.index', [
        ...request()->except(['industry', 'page']),
        ...($value ? ['industry' => $value] : []),
    ]);

    // Capacity ring
    $r = 42;
    $circ = 2 * M_PI * $r;
    $dash = $circ * $stats['percent'] / 100;
@endphp

<x-layouts.app title="Partner companies">

    {{-- Header --}}
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wide text-blue-600">Industry network</p>
            <h1 class="mt-2 text-4xl font-normal tracking-tight text-slate-900">Partner companies</h1>
            <p class="mt-2 text-sm text-slate-500">Manage host organizations, placement capacity, and supervisor relationships.</p>
        </div>
@if ($isAdmin)
        <a href="{{ route('companies.create') }}"
           class="rounded-xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-blue-600/25 transition hover:bg-blue-700">
            + Add partner
        </a>
        @endif
    </div>
    @if (session('status'))
    <div class="mt-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('status') }}</div>
@endif
@if (session('error'))
    <div class="mt-6 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">{{ session('error') }}</div>
@endif

    {{-- Summary cards --}}
    <div class="mt-8 grid grid-cols-1 gap-5 lg:grid-cols-[1.4fr_1fr_1fr]">

        {{-- Placement capacity --}}
        <div class="flex items-center justify-between rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <div>
                <p class="text-xs text-slate-500">Placement capacity</p>
                <p class="mt-2 text-4xl font-semibold text-slate-900">{{ $stats['percent'] }}%</p>
                <p class="mt-3 text-[11px] text-slate-400">{{ $stats['filled'] }} of {{ $stats['capacity'] }} slots filled</p>
            </div>

            <div class="relative h-24 w-24 shrink-0">
                <svg viewBox="0 0 100 100" class="h-full w-full -rotate-90">
                    <circle cx="50" cy="50" r="{{ $r }}" fill="none" class="stroke-blue-100" stroke-width="10" />
                    <circle cx="50" cy="50" r="{{ $r }}" fill="none" class="stroke-blue-600" stroke-width="10"
                            stroke-linecap="round"
                            stroke-dasharray="{{ round($dash, 2) }} {{ round($circ - $dash, 2) }}" />
                </svg>
                <div class="absolute inset-0 flex flex-col items-center justify-center leading-tight">
                    <span class="text-sm font-semibold text-slate-700">{{ $stats['open'] }}</span>
                    <span class="text-[9px] text-slate-400">open slots</span>
                </div>
            </div>
        </div>

        {{-- Active partners --}}
        <div class="flex items-center gap-4 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <span class="flex h-14 w-14 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
                <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21" />
                </svg>
            </span>
            <div>
                <p class="text-xs text-slate-500">Active partners</p>
                <p class="text-3xl font-semibold leading-tight text-slate-900">{{ $stats['partners'] }}</p>
                <p class="text-[11px] text-emerald-600">+{{ $stats['added'] }} this semester</p>
            </div>
        </div>

        {{-- Avg rating --}}
        <div class="flex items-center gap-4 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <span class="flex h-14 w-14 shrink-0 items-center justify-center rounded-xl bg-violet-50 text-violet-600">
                <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </span>
            <div>
                <p class="text-xs text-slate-500">Avg. partner rating</p>
                <p class="text-3xl font-semibold leading-tight text-slate-900">{{ $stats['rating'] }}</p>
                <p class="text-[11px] text-emerald-600">{{ $stats['ratingNote'] }}</p>
            </div>
        </div>
    </div>

    {{-- List header --}}
    <div class="mt-10 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h2 class="text-lg text-slate-900">All partners</h2>
            <p class="mt-1 text-xs text-slate-400">{{ $companies->total() }} {{ Str::plural('organization', $companies->total()) }} shown</p>
        </div>

        {{-- Industry filter (Alpine.js) --}}
        <div x-data="{ open: false }" class="relative">
            <button type="button" @click="open = !open"
                    class="rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-medium text-slate-600 shadow-sm hover:bg-slate-50">
                Industry: {{ $industry ?: 'All' }}
            </button>

            <div x-cloak x-show="open" x-transition.opacity
                 @click.outside="open = false" @keydown.escape.window="open = false"
                 class="absolute right-0 z-20 mt-2 w-60 overflow-hidden rounded-xl border border-slate-200 bg-white py-1 shadow-lg">
                <a href="{{ $filterUrl(null) }}"
                   @class(['block px-4 py-2 text-sm hover:bg-slate-50', 'font-semibold text-blue-700' => ! $industry, 'text-slate-600' => $industry])>
                    All
                </a>
                @foreach ($industries as $name)
                    <a href="{{ $filterUrl($name) }}"
                       @class(['block px-4 py-2 text-sm hover:bg-slate-50', 'font-semibold text-blue-700' => $industry === $name, 'text-slate-600' => $industry !== $name])>
                        {{ $name }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Company cards --}}
    <div class="mt-5 grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-3">
        @forelse ($companies as $company)
            <article class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex items-start justify-between">
                    <span class="flex h-14 w-14 items-center justify-center rounded-xl text-sm font-semibold {{ $tones[$company->id % count($tones)] }}">
                        {{ $company->initials }}
                    </span>

                    {{-- Card menu (Alpine.js) --}}
                    <div x-data="{ open: false }" class="relative">
                        <button type="button" @click="open = !open" class="text-slate-400 hover:text-slate-600" aria-label="More options">
                            <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                                <circle cx="5" cy="12" r="1.6" /><circle cx="12" cy="12" r="1.6" /><circle cx="19" cy="12" r="1.6" />
                            </svg>
                        </button>
                        <div x-cloak x-show="open" x-transition.opacity @click.outside="open = false"
                             class="absolute right-0 z-10 mt-1 w-40 overflow-hidden rounded-xl border border-slate-200 bg-white py-1 text-sm shadow-lg">
                            <a href="{{ Route::has('companies.show') ? route('companies.show', $company) : '#' }}" class="block px-4 py-2 text-slate-600 hover:bg-slate-50">View details</a>
                            
                           @if ($isAdmin)
    <a href="{{ route('companies.edit', $company) }}" class="block px-4 py-2 text-slate-600 hover:bg-slate-50">Edit</a>
    <form method="POST" action="{{ route('companies.destroy', $company) }}"
          onsubmit="return confirm('Delete {{ addslashes($company->name) }}?')">
        @csrf @method('DELETE')
        <button class="block w-full px-4 py-2 text-left text-rose-600 hover:bg-rose-50">Delete</button>
    </form>
@endif
                        </div>
                    </div>
                </div>

                <h3 class="mt-5 text-base font-medium text-slate-900">{{ $company->name }}</h3>
                <p class="mt-1 text-xs text-slate-400">{{ $company->industry ?? '—' }}</p>

                <div class="mt-5 grid grid-cols-3 border-t border-slate-100 pt-4">
                    <div>
                        <p class="text-sm font-semibold text-slate-900">{{ $company->students_count }}</p>
                        <p class="mt-1 text-[10px] text-slate-400">Active interns</p>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-slate-900">{{ $company->open_slots }}</p>
                        <p class="mt-1 text-[10px] text-slate-400">Open slots</p>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-slate-900">★ {{ $company->rating ?? '—' }}</p>
                        <p class="mt-1 text-[10px] text-slate-400">Rating</p>
                    </div>
                </div>

                <a href="{{ Route::has('companies.show') ? route('companies.show', $company) : '#' }}"
                   class="mt-4 flex items-center justify-between border-t border-slate-100 pt-4">
                    <div class="flex items-center gap-3">
                        <span class="flex h-10 w-10 items-center justify-center rounded-full bg-slate-100 text-[11px] font-semibold text-slate-500">
                            {{ $company->supervisor_initials ?: '—' }}
                        </span>
                        <div>
                            <p class="text-[10px] text-slate-400">Primary supervisor</p>
                            <p class="text-sm font-semibold text-slate-800">{{ $company->supervisor_name ?? 'Unassigned' }}</p>
                        </div>
                    </div>
                    <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                    </svg>
                </a>
            </article>
        @empty
            <p class="col-span-full rounded-2xl border border-dashed border-slate-300 bg-white py-16 text-center text-sm text-slate-400">
                No partner companies match this filter.
            </p>
        @endforelse
    </div>

    @if ($companies->hasPages())
        <div class="mt-8">
            {{ $companies->links() }}
        </div>
    @endif
</x-layouts.app>