@php
    $hourPx = 60;   // keep equal to HOUR_PX in the controller
    $cols   = 'grid-cols-[4rem_repeat(5,minmax(0,1fr))]';
@endphp

<x-layouts.app title="Schedule">
    {{-- One Alpine scope so the header button and the modal share state --}}
    <div x-data="{ open: @js($errors->any()) }">

        {{-- Header --}}
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-blue-600">Coordination calendar</p>
                <h1 class="mt-2 text-4xl font-normal tracking-tight text-slate-900">Schedule</h1>
                <p class="mt-2 text-sm text-slate-500">Plan check-ins, employer visits, deadlines, and intervention meetings.</p>
            </div>

            <button type="button" @click="open = true"
                    class="rounded-xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-blue-600/25 transition hover:bg-blue-700">
                + New event
            </button>
        </div>

        @if (session('status'))
            <div class="mt-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                {{ session('status') }}
            </div>
        @endif

        <div class="mt-8 grid grid-cols-1 gap-6 lg:grid-cols-[2.3fr_1fr]">

            {{-- Week calendar --}}
            <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

                {{-- Toolbar --}}
                <div class="flex items-center justify-between gap-3 p-5">
                    <div class="flex items-center gap-3">
                        <a href="{{ route('schedule.index', ['week' => $prevWeek]) }}" aria-label="Previous week"
                           class="flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 text-slate-500 hover:bg-slate-50">‹</a>
                        <span class="text-sm font-medium text-slate-800">{{ $label }}</span>
                        <a href="{{ route('schedule.index', ['week' => $nextWeek]) }}" aria-label="Next week"
                           class="flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 text-slate-500 hover:bg-slate-50">›</a>
                    </div>

                    <a href="{{ route('schedule.index') }}"
                       class="rounded-xl border border-slate-200 px-5 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-50">
                        Today
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <div class="min-w-[720px]">

                        {{-- Day headers --}}
                        <div class="grid {{ $cols }} border-y border-slate-200">
                            <div></div>
                            @foreach ($days as $day)
                                <div class="flex items-center justify-center gap-2 border-l border-slate-100 py-4">
                                    <span class="text-[11px] uppercase text-slate-400">{{ $day['date']->format('D') }}</span>
                                    <span @class([
                                        'flex h-8 w-8 items-center justify-center rounded-full text-sm font-semibold',
                                        'bg-blue-600 text-white' => $day['isToday'],
                                        'text-slate-500'         => ! $day['isToday'],
                                    ])>{{ $day['date']->format('j') }}</span>
                                </div>
                            @endforeach
                        </div>

                        {{-- Time grid --}}
                        <div class="grid {{ $cols }}" style="height: {{ $gridHeight * $hourPx / 60 }}px">

                            {{-- Hour labels --}}
                            <div class="relative">
                                @foreach ($hours as $i => $hour)
                                    <span class="absolute right-3 text-[10px] text-slate-400" style="top: {{ $i * $hourPx + 10 }}px">
                                        {{ \Illuminate\Support\Carbon::createFromTime($hour)->format('g A') }}
                                    </span>
                                @endforeach
                            </div>

                            {{-- Day columns --}}
                            @foreach ($days as $day)
                                <div class="relative border-l border-slate-100"
                                     style="background-image: linear-gradient(to bottom, #f1f5f9 1px, transparent 1px); background-size: 100% {{ $hourPx }}px;">

                                    @foreach ($day['items'] as $item)
                                        @php $event = $item['event']; @endphp
                                        <div class="absolute overflow-hidden rounded-lg border-l-[3px] px-2.5 py-1.5 {{ $event->type->block() }}"
                                             style="top: {{ $item['top'] }}px; height: {{ $item['height'] - 2 }}px; left: calc({{ $item['left'] }}% + 4px); width: calc({{ $item['width'] }}% - 8px);"
                                             title="{{ $event->title }} — {{ $event->subtitle }}">
                                            <p class="truncate text-[11px] font-semibold">{{ $event->title }}</p>
                                            <p class="truncate text-[10px] opacity-70">
                                                {{ $event->starts_at->format('g:i A') }}@if ($event->location) · {{ $event->location }}@endif
                                            </p>
                                        </div>
                                    @endforeach

                                    @if ($day['isToday'] && $nowTop !== null)
                                        <div class="absolute inset-x-0 z-10 flex items-center" style="top: {{ $nowTop * $hourPx / 60 }}px">
                                            <span class="-ml-1 h-2 w-2 rounded-full bg-rose-500"></span>
                                            <span class="h-px flex-1 bg-rose-500"></span>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </section>

            {{-- Right column --}}
            <div class="space-y-6">

                {{-- Today --}}
                <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg text-slate-900">Today</h2>
                        <span class="rounded-full bg-blue-50 px-3 py-1 text-[11px] font-semibold text-blue-700">
                            {{ $todayEvents->count() }} {{ \Illuminate\Support\Str::plural('event', $todayEvents->count()) }}
                        </span>
                    </div>
                    <p class="mt-1 text-xs text-slate-400">{{ today()->format('l, F j') }}</p>

                    <ul class="mt-6 divide-y divide-slate-100">
                        @forelse ($todayEvents as $event)
                            <li class="flex gap-4 py-4 first:pt-0">
                                <span class="w-0.5 shrink-0 rounded-full {{ $event->type->bar() }}"></span>
                                <div class="min-w-0">
                                    <p class="text-[10px] text-slate-400">{{ $event->starts_at->format('g:i A') }}</p>
                                    <p class="text-sm font-semibold text-slate-900">{{ $event->title }}</p>
                                    @if ($event->subtitle)
                                        <p class="truncate text-[11px] text-slate-400">{{ $event->subtitle }}</p>
                                    @endif
                                </div>
                            </li>
                        @empty
                            <li class="py-8 text-center text-sm text-slate-400">Nothing scheduled today.</li>
                        @endforelse
                    </ul>
                </section>

                {{-- Important deadline --}}
                <section class="flex items-start gap-4 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <span class="flex h-14 w-14 shrink-0 items-center justify-center rounded-xl bg-rose-50 text-rose-500">
                        <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                        </svg>
                    </span>
                    <div>
                        <p class="text-[10px] uppercase tracking-wide text-slate-400">Important deadline</p>

                        @if ($deadline)
                            <p class="mt-1 text-base font-medium text-slate-900">{{ $deadline['title'] }}</p>
                            <p class="mt-1 text-[11px] text-slate-400">
                                {{ $deadline['remaining'] }} {{ \Illuminate\Support\Str::plural('submission', $deadline['remaining']) }} remaining
                            </p>
                            <span class="mt-4 inline-block rounded-full bg-rose-50 px-3 py-1 text-[11px] font-semibold text-rose-600">
                                {{ $deadline['label'] }}
                            </span>
                        @else
                            <p class="mt-1 text-sm text-slate-500">No upcoming deadlines.</p>
                        @endif
                    </div>
                </section>
            </div>
        </div>

        {{-- New event modal (Alpine.js) --}}
        <div x-cloak x-show="open" x-transition.opacity @keydown.escape.window="open = false"
             class="fixed inset-0 z-40 flex items-center justify-center bg-slate-900/40 p-4">
            <div @click.outside="open = false" class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-xl">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-slate-900">New event</h2>
                    <button type="button" @click="open = false" class="text-slate-400 hover:text-slate-600" aria-label="Close">✕</button>
                </div>

                <form method="POST" action="{{ route('schedule.store') }}" class="mt-5 space-y-4">
                    @csrf

                    <label class="block">
                        <span class="text-xs font-semibold text-slate-500">Title</span>
                        <input name="title" value="{{ old('title') }}" required
                               class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                        @error('title') <span class="text-xs text-rose-600">{{ $message }}</span> @enderror
                    </label>

                    <div class="grid grid-cols-2 gap-4">
                        <label class="block">
                            <span class="text-xs font-semibold text-slate-500">Type</span>
                            <select name="type" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm">
                                @foreach ($types as $type)
                                    <option value="{{ $type->value }}" @selected(old('type') === $type->value)>{{ $type->label() }}</option>
                                @endforeach
                            </select>
                            @error('type') <span class="text-xs text-rose-600">{{ $message }}</span> @enderror
                        </label>

                        <label class="block">
                            <span class="text-xs font-semibold text-slate-500">Date</span>
                            <input type="date" name="date" value="{{ old('date', today()->toDateString()) }}" required
                                   class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm">
                            @error('date') <span class="text-xs text-rose-600">{{ $message }}</span> @enderror
                        </label>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <label class="block">
                            <span class="text-xs font-semibold text-slate-500">Start time</span>
                            <input type="time" name="start_time" value="{{ old('start_time', '09:00') }}" required
                                   class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm">
                            @error('start_time') <span class="text-xs text-rose-600">{{ $message }}</span> @enderror
                        </label>

                        <label class="block">
                            <span class="text-xs font-semibold text-slate-500">End time (optional)</span>
                            <input type="time" name="end_time" value="{{ old('end_time') }}"
                                   class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm">
                            @error('end_time') <span class="text-xs text-rose-600">{{ $message }}</span> @enderror
                        </label>
                    </div>

                    <label class="block">
                        <span class="text-xs font-semibold text-slate-500">Location (optional)</span>
                        <input name="location" value="{{ old('location') }}" placeholder="Room 204, Online, On-site…"
                               class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm">
                        @error('location') <span class="text-xs text-rose-600">{{ $message }}</span> @enderror
                    </label>

                    <label class="block">
                        <span class="text-xs font-semibold text-slate-500">Student (optional)</span>
                        <select name="student_id" class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm">
                            <option value="">— None —</option>
                            @foreach ($students as $student)
                                <option value="{{ $student->id }}" @selected((string) old('student_id') === (string) $student->id)>
                                    {{ $student->last_name }}, {{ $student->first_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('student_id') <span class="text-xs text-rose-600">{{ $message }}</span> @enderror
                    </label>

                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" @click="open = false"
                                class="rounded-lg px-4 py-2 text-sm font-medium text-slate-500 hover:text-slate-800">Cancel</button>
                        <button class="rounded-lg bg-blue-600 px-5 py-2 text-sm font-semibold text-white hover:bg-blue-700">Save event</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>