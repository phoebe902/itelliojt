@php
    $chevron = 'M8.25 4.5l7.5 7.5-7.5 7.5';
    $tabUrl  = fn (?\App\Enums\RiskLevel $level) => route('students.index', [
        ...request()->except(['risk', 'page']),
        ...($level ? ['risk' => $level->value] : []),
    ]);
@endphp

<x-layouts.app title="Students">

    {{-- Header --}}
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wide text-blue-600">Intern directory</p>
            <h1 class="mt-2 text-4xl font-normal tracking-tight text-slate-900">Students</h1>
            <p class="mt-2 text-sm text-slate-500">Monitor student placement, training hours, and performance signals.</p>
        </div>

        <a href="{{ Route::has('students.create') ? route('students.create') : '#' }}"
           class="rounded-xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-blue-600/25 transition hover:bg-blue-700">
            + Add student
        </a>
    </div>

    {{-- Summary strip --}}
    <div class="mt-8 grid grid-cols-2 divide-x divide-slate-200 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm lg:grid-cols-4">
        <div class="flex items-start gap-3 p-6">
            <span class="mt-2 h-2.5 w-2.5 rounded-full bg-blue-600"></span>
            <div>
                <p class="text-2xl font-semibold text-slate-900">{{ $total }}</p>
                <p class="text-xs text-slate-400">Total interns</p>
            </div>
        </div>

        @foreach (\App\Enums\RiskLevel::cases() as $level)
            <div class="flex items-start gap-3 p-6">
                <span class="mt-2 h-2.5 w-2.5 rounded-full {{ $level->dot() }}"></span>
                <div>
                    <p class="text-2xl font-semibold text-slate-900">{{ $counts[$level->value] ?? 0 }}</p>
                    <p class="text-xs text-slate-400">{{ $level->label() }}</p>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Table card --}}
    <section class="mt-6 rounded-2xl border border-slate-200 bg-white shadow-sm">

        {{-- Toolbar --}}
        <div class="flex flex-wrap items-center justify-between gap-3 p-5">
            <nav class="flex flex-wrap gap-1">
                @php $tabs = [[null, 'All students'], ...array_map(fn ($l) => [$l, $l->label()], \App\Enums\RiskLevel::cases())]; @endphp
                @foreach ($tabs as [$level, $label])
                    <a href="{{ $tabUrl($level) }}"
                       @class([
                           'rounded-lg px-4 py-2 text-xs font-semibold transition',
                           'bg-blue-50 text-blue-700' => $risk === $level,
                           'text-slate-500 hover:bg-slate-50 hover:text-slate-800' => $risk !== $level,
                       ])>{{ $label }}</a>
                @endforeach
            </nav>

            {{-- Filter & sort (Alpine.js) --}}
            <div x-data="{ open: false }" class="relative">
                <button type="button" @click="open = !open"
                        class="rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-50">
                    Filter &amp; sort
                </button>

                <div x-cloak x-show="open" x-transition.opacity
                     @click.outside="open = false" @keydown.escape.window="open = false"
                     class="absolute right-0 z-20 mt-2 w-72 rounded-xl border border-slate-200 bg-white p-4 shadow-lg">
                    <form method="GET" action="{{ route('students.index') }}" class="space-y-4">
                        @if ($risk)
                            <input type="hidden" name="risk" value="{{ $risk->value }}">
                        @endif

                        <label class="block">
                            <span class="text-xs font-semibold text-slate-500">Search</span>
                            <input type="search" name="search" value="{{ $search }}" placeholder="Name, ID or company"
                                   class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                        </label>

                        <label class="block">
                            <span class="text-xs font-semibold text-slate-500">Sort by</span>
                            <select name="sort" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm">
                                @foreach ($sorts as $key => $label)
                                    <option value="{{ $key }}" @selected($sort === $key)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </label>

                        <label class="block">
                            <span class="text-xs font-semibold text-slate-500">Direction</span>
                            <select name="dir" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm">
                                <option value="asc"  @selected($dir === 'asc')>Ascending</option>
                                <option value="desc" @selected($dir === 'desc')>Descending</option>
                            </select>
                        </label>

                        <div class="flex items-center justify-between pt-1">
                            <a href="{{ route('students.index') }}" class="text-sm text-slate-500 hover:text-slate-800">Reset</a>
                            <button class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">Apply</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="border-y border-slate-200 bg-slate-50 text-[11px] font-semibold uppercase tracking-wider text-slate-400">
                    <tr>
                        <th class="px-6 py-4">Student</th>
                        <th class="px-6 py-4">Placement</th>
                        <th class="px-6 py-4">OJT progress</th>
                        <th class="px-6 py-4">Hours</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4"></th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">
                    @forelse ($students as $student)
                        <tr class="transition hover:bg-slate-50/70">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-4">
                                    <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl text-xs font-semibold {{ $student->risk_level->avatar() }}">
                                        {{ $student->initials }}
                                    </span>
                                    <div>
                                        <p class="text-sm font-semibold text-slate-900">{{ $student->full_name }}</p>
                                        <p class="text-[11px] text-slate-400">{{ $student->student_number }}</p>
                                    </div>
                                </div>
                            </td>

                            <td class="px-6 py-4 text-sm text-slate-500">
                                {{ $student->company?->name ?? 'Unplaced' }}
                            </td>

                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="h-1.5 w-40 overflow-hidden rounded-full bg-slate-200">
                                        <div class="h-full rounded-full bg-blue-600" style="width: {{ $student->progress }}%"></div>
                                    </div>
                                    <span class="text-xs text-slate-400">{{ $student->progress }}%</span>
                                </div>
                            </td>

                            <td class="px-6 py-4 text-sm font-semibold text-slate-700">
                                {{ $student->rendered_hours }} / {{ $student->required_hours }}
                            </td>

                            <td class="px-6 py-4">
                                <span class="inline-block rounded-full px-3 py-1 text-[11px] font-semibold {{ $student->risk_level->badge() }}">
                                    {{ $student->risk_level->label() }}
                                </span>
                            </td>

                            <td class="px-6 py-4 text-right">
                                <a href="{{ Route::has('students.show') ? route('students.show', $student) : '#' }}"
                                   class="text-slate-400 hover:text-slate-700" aria-label="View {{ $student->full_name }}">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="{{ $chevron }}" />
                                    </svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center text-sm text-slate-400">
                                No students match your filters.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($students->hasPages())
            <div class="border-t border-slate-100 px-6 py-4">
                {{ $students->links() }}
            </div>
        @endif
    </section>
</x-layouts.app>