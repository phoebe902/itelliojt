@php
    $rankTones = ['bg-rose-50 text-rose-500', 'bg-amber-50 text-amber-600', 'bg-blue-50 text-blue-600'];

    // Forecast ring
    $r    = 70;
    $circ = 2 * M_PI * $r;
    $dash = $circ * min(100, $forecast['success']) / 100;

    $barMax = max($impact['bars']->max('rate'), 1);
    $up     = $impact['delta'] >= 0;
@endphp

<x-layouts.app title="Analytics">

    {{-- Header --}}
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wide text-blue-600">Predictive intelligence</p>
            <h1 class="mt-2 text-4xl font-normal tracking-tight text-slate-900">Analytics</h1>
            <p class="mt-2 text-sm text-slate-500">Understand performance trends, skill gaps, and intervention outcomes.</p>
        </div>

        <button type="button" onclick="window.print()"
                class="rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-600 shadow-sm hover:bg-slate-50 print:hidden">
            Export report
        </button>
    </div>

    {{-- Row 1: forecast + intervention impact --}}
    <div class="mt-8 grid grid-cols-1 gap-6 lg:grid-cols-[2.4fr_1fr]">

        {{-- Predictive forecast --}}
        <section class="flex flex-wrap items-center justify-between gap-8 rounded-2xl bg-gradient-to-br from-[#10285f] via-[#12388a] to-[#1a4aa0] p-10 text-white shadow-sm">
            <div class="max-w-xl">
                <span class="inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-3 py-1.5 text-[10px] font-semibold uppercase tracking-wider text-white/80">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z" />
                    </svg>
                    Predictive forecast
                </span>

                <h2 class="mt-6 text-3xl font-normal leading-snug">
                    {{ round($forecast['success']) }}% of this cohort is on track to complete OJT successfully.
                </h2>
                <p class="mt-4 text-xs text-white/75">
                    Based on attendance, task velocity, supervisor ratings, and weekly log patterns.
                </p>

                <div class="mt-6 flex flex-wrap items-center gap-5">
                    <a href="#" class="rounded-xl bg-white px-5 py-3 text-sm font-semibold text-slate-900 hover:bg-slate-100">
                        View model insights
                    </a>
                    <span class="text-[11px] text-white/60">Last recalculated {{ $forecast['calculated'] }}</span>
                </div>
            </div>

            <div class="mx-auto text-center">
                <div class="relative h-40 w-40">
                    <svg viewBox="0 0 160 160" class="h-full w-full -rotate-90">
                        <circle cx="80" cy="80" r="{{ $r }}" fill="none" class="stroke-white/15" stroke-width="10" />
                        <circle cx="80" cy="80" r="{{ $r }}" fill="none" class="stroke-teal-300" stroke-width="10"
                                stroke-linecap="round"
                                stroke-dasharray="{{ round($dash, 2) }} {{ round($circ - $dash, 2) }}" />
                    </svg>
                    <div class="absolute inset-0 flex flex-col items-center justify-center">
                        <span class="text-4xl font-semibold">{{ round($forecast['success']) }}%</span>
                        <span class="text-[10px] text-white/70">success forecast</span>
                    </div>
                </div>
                <p class="mt-3 text-[11px] text-white/60">{{ round($forecast['confidence']) }}% model confidence</p>
            </div>
        </section>

        {{-- Intervention impact --}}
        <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg text-slate-900">Intervention impact</h2>
            <p class="mt-1 text-xs text-slate-400">Students improved after action</p>

            <p class="mt-8 text-5xl font-semibold tracking-tight text-slate-900">{{ round($impact['current']) }}%</p>
            <p @class(['mt-2 flex items-center gap-1 text-[11px]', 'text-emerald-600' => $up, 'text-rose-500' => ! $up])>
                <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="{{ $up ? 'M4.5 10.5L12 3m0 0l7.5 7.5M12 3v18' : 'M19.5 13.5L12 21m0 0l-7.5-7.5M12 21V3' }}" />
                </svg>
                {{ abs(round($impact['delta'])) }}% vs last semester
            </p>

            <div class="mt-6 flex h-20 items-end gap-2">
                @foreach ($impact['bars'] as $bar)
                    <div @class(['flex-1 rounded-t-md', 'bg-blue-500' => $loop->last, 'bg-blue-100' => ! $loop->last])
                         style="height: {{ max(8, $bar['rate'] / $barMax * 100) }}%"
                         title="{{ $bar['label'] }}: {{ round($bar['rate']) }}%"></div>
                @endforeach
            </div>
        </section>
    </div>

    {{-- Row 2: competencies + risk signals --}}
    <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-[2fr_1fr]">

        {{-- Competency performance --}}
        <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <header class="flex items-start justify-between">
                <div>
                    <h2 class="text-lg text-slate-900">Competency performance</h2>
                    <p class="mt-1 text-xs text-slate-400">Cohort scores compared with industry benchmarks</p>
                </div>
                <div class="flex items-center gap-4 text-[10px] text-slate-400">
                    <span class="flex items-center gap-1.5"><span class="h-2 w-2 rounded-sm bg-blue-600"></span>Cohort</span>
                    <span class="flex items-center gap-1.5"><span class="h-2 w-2 rounded-sm bg-slate-300"></span>Benchmark</span>
                </div>
            </header>

            <div class="mt-8 space-y-5">
                @forelse ($competencies as $c)
                    @php $score = (int) round($c->cohort_score ?? 0); @endphp
                    <div class="flex items-center gap-4">
                        <span class="w-36 shrink-0 text-xs text-slate-500">{{ $c->name }}</span>

                        <div class="relative h-2 flex-1 rounded-full bg-slate-200">
                            <div class="h-full rounded-full bg-gradient-to-r from-blue-700 to-blue-500" style="width: {{ $score }}%"></div>
                            <span class="absolute top-1/2 h-4 w-0.5 -translate-x-1/2 -translate-y-1/2 bg-slate-500"
                                  style="left: {{ $c->benchmark }}%" title="Benchmark {{ $c->benchmark }}%"></span>
                        </div>

                        <span class="w-10 text-right text-xs font-semibold text-slate-600">{{ $score }}%</span>
                    </div>
                @empty
                    <p class="py-8 text-center text-sm text-slate-400">No competency data yet.</p>
                @endforelse
            </div>
        </section>

        {{-- Top risk signals --}}
        <section class="flex flex-col rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg text-slate-900">Top risk signals</h2>
            <p class="mt-1 text-xs text-slate-400">Factors influencing current alerts</p>

            <ul class="mt-6 flex-1 divide-y divide-slate-100">
                @forelse ($signals as $signal)
                    <li class="flex items-center gap-4 py-4">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg text-[11px] font-semibold {{ $rankTones[$loop->index] ?? $rankTones[2] }}">
                            {{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}
                        </span>
                        <div class="flex-1">
                            <p class="text-sm font-semibold text-slate-800">{{ $signal->label }}</p>
                            <p class="text-[10px] text-slate-400">
                                Present in {{ $signal->students_count }} student {{ Str::plural('profile', $signal->students_count) }}
                            </p>
                        </div>
                        <span class="text-xs font-semibold text-slate-600">
                            {{ $atRiskCount ? round($signal->students_count / $atRiskCount * 100) : 0 }}%
                        </span>
                    </li>
                @empty
                    <li class="py-8 text-center text-sm text-slate-400">No risk signals recorded.</li>
                @endforelse
            </ul>

            <a href="#" class="mt-4 inline-flex items-center gap-1 text-xs font-semibold text-blue-700 hover:text-blue-800">
                Explore decision tree
                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
            </a>
        </section>
    </div>
</x-layouts.app>