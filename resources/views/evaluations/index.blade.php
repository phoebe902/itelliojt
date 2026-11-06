@php
    $icons = [
        'clock'    => 'M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z',
        'calendar' => 'M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5',
        'check'    => 'M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        'chevron'  => 'M8.25 4.5l7.5 7.5-7.5 7.5',
    ];

    $cards = [
        ['icon' => 'clock',    'tone' => 'bg-blue-50 text-blue-600',       'value' => $stats['awaiting'],  'label' => 'Awaiting review',    'note' => 'Requires your attention'],
        ['icon' => 'calendar', 'tone' => 'bg-slate-50 text-slate-700',     'value' => $stats['dueSoon'],   'label' => 'Pending submission', 'note' => 'Due within 7 days'],
        ['icon' => 'check',    'tone' => 'bg-emerald-50 text-emerald-600', 'value' => $stats['submitted'], 'label' => 'Completed',          'note' => $stats['percent'] . '% completion rate'],
    ];

    // Progress ring
    $r    = 80;
    $circ = 2 * M_PI * $r;
    $dash = $circ * $stats['percent'] / 100;

    $legend = [
        ['Submitted', $stats['submitted'], 'bg-emerald-500'],
        ['Pending',   $stats['pending'],   'bg-amber-400'],
        ['Overdue',   $stats['overdue'],   'bg-rose-500'],
    ];
@endphp

<x-layouts.app title="Evaluations">

    {{-- Header --}}
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wide text-blue-600">Review center</p>
            <h1 class="mt-2 text-4xl font-normal tracking-tight text-slate-900">Evaluations</h1>
            <p class="mt-2 text-sm text-slate-500">Review employer feedback and monitor outstanding assessment requests.</p>
        </div>

        <form method="POST" action="{{ route('evaluations.remind') }}"
              onsubmit="return confirm('Send a reminder to every evaluator with a pending evaluation?')">
            @csrf
            <input type="hidden" name="term" value="{{ $term }}">
            <button class="rounded-xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-blue-600/25 transition hover:bg-blue-700">
                Send reminder
            </button>
        </form>
    </div>

    @if (session('status'))
        <div class="mt-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
            {{ session('status') }}
        </div>
    @endif

    {{-- Stat cards --}}
    <div class="mt-8 grid grid-cols-1 gap-5 md:grid-cols-3">
        @foreach ($cards as $card)
            <div class="flex items-center justify-between gap-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex items-center gap-4">
                    <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl {{ $card['tone'] }}">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                            <path d="{{ $icons[$card['icon']] }}" />
                        </svg>
                    </span>
                    <div>
                        <p class="text-3xl font-semibold leading-tight text-slate-900">{{ $card['value'] }}</p>
                        <p class="text-xs text-slate-500">{{ $card['label'] }}</p>
                    </div>
                </div>
                <p class="self-start pt-3 text-right text-[10px] text-slate-400">{{ $card['note'] }}</p>
            </div>
        @endforeach
    </div>

    {{-- Queue + progress --}}
    <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-[2.2fr_1fr]">

        {{-- Evaluation queue --}}
        <section class="rounded-2xl border border-slate-200 bg-white shadow-sm">
            <header class="flex items-start justify-between border-b border-slate-100 p-6">
                <div>
                    <h2 class="text-lg text-slate-900">Evaluation queue</h2>
                    <p class="mt-1 text-xs text-slate-400">Recently submitted employer assessments</p>
                </div>
                <a href="{{ route('evaluations.index', $showAll ? [] : ['all' => 1]) }}"
                   class="text-xs font-semibold tracking-wide text-slate-400 hover:text-blue-700">
                    {{ $showAll ? 'Recent only' : 'All submissions' }}
                </a>
            </header>

            <ul class="divide-y divide-slate-100">
                @forelse ($evaluations as $evaluation)
                    @php $student = $evaluation->student; @endphp
                    <li class="flex items-center gap-4 px-6 py-4 transition hover:bg-slate-50/70">
                        {{-- Student --}}
                        <div class="flex min-w-0 flex-1 items-center gap-4">
                            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl text-xs font-semibold {{ $evaluation->status->avatar() }}">
                                {{ $student->initials }}
                            </span>
                            <div class="min-w-0">
                                <p class="truncate text-sm font-semibold text-slate-900">{{ $student->full_name }}</p>
                                <p class="truncate text-[11px] text-slate-400">{{ $student->company?->name ?? 'Unplaced' }}</p>
                            </div>
                        </div>

                        {{-- Evaluator --}}
                        <div class="hidden w-36 md:block">
                            <p class="text-[10px] text-slate-400">Evaluator</p>
                            <p class="text-sm font-semibold text-slate-800">{{ $evaluation->evaluator_name ?? '—' }}</p>
                        </div>

                        {{-- Submitted --}}
                        <div class="hidden w-24 md:block">
                            <p class="text-[10px] text-slate-400">Submitted</p>
                            <p class="text-sm font-semibold text-slate-800">{{ $evaluation->submitted_on?->format('M j') ?? '—' }}</p>
                        </div>

                        {{-- Score --}}
                        <div class="hidden w-14 sm:block">
                            <p class="text-[10px] text-slate-400">Score</p>
                            <p class="text-sm font-semibold text-blue-600">{{ $evaluation->score ?? '—' }}</p>
                        </div>

                        {{-- Status --}}
                        <span class="hidden w-32 rounded-full px-4 py-1.5 text-center text-[11px] font-semibold sm:inline-block {{ $evaluation->status->badge() }}">
                            {{ $evaluation->status->label() }}
                        </span>

                        <a href="{{ Route::has('evaluations.show') ? route('evaluations.show', $evaluation) : '#' }}"
                           class="text-slate-400 hover:text-slate-700" aria-label="Open evaluation for {{ $student->full_name }}">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <path d="{{ $icons['chevron'] }}" />
                            </svg>
                        </a>
                    </li>
                @empty
                    <li class="px-6 py-16 text-center text-sm text-slate-400">No submitted evaluations yet.</li>
                @endforelse
            </ul>

            @if ($showAll && $evaluations->hasPages())
                <div class="border-t border-slate-100 px-6 py-4">
                    {{ $evaluations->links() }}
                </div>
            @endif
        </section>

        {{-- Submission progress --}}
        <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg text-slate-900">Submission progress</h2>
            <p class="mt-1 text-xs text-slate-400">{{ \Illuminate\Support\Str::headline($term) }} evaluations</p>

            <div class="relative mx-auto mt-6 h-52 w-52">
                <svg viewBox="0 0 200 200" class="h-full w-full -rotate-90">
                    <circle cx="100" cy="100" r="{{ $r }}" fill="none" class="stroke-slate-100" stroke-width="18" />
                    <circle cx="100" cy="100" r="{{ $r }}" fill="none" class="stroke-emerald-500" stroke-width="18"
                            stroke-linecap="round"
                            stroke-dasharray="{{ round($dash, 2) }} {{ round($circ - $dash, 2) }}" />
                </svg>
                <div class="absolute inset-0 flex flex-col items-center justify-center">
                    <span class="text-4xl font-semibold text-slate-900">{{ $stats['percent'] }}%</span>
                    <span class="text-[10px] text-slate-400">{{ $stats['submitted'] }} of {{ $stats['total'] }}</span>
                </div>
            </div>

            <div class="mt-6 flex items-center gap-3 rounded-xl bg-blue-50 p-4">
                <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                    <path d="{{ $icons['calendar'] }}" />
                </svg>
                <div>
                    <p class="text-[10px] text-slate-400">Next deadline</p>
                    <p class="text-sm font-semibold text-slate-800">{{ $nextDeadline?->format('F j, Y') ?? 'None scheduled' }}</p>
                </div>
            </div>

            <ul class="mt-6 space-y-3">
                @foreach ($legend as [$label, $count, $dot])
                    <li class="flex items-center justify-between text-xs">
                        <span class="flex items-center gap-2 text-slate-500">
                            <span class="h-2 w-2 rounded-full {{ $dot }}"></span>{{ $label }}
                        </span>
                        <span class="font-semibold text-slate-800">{{ $count }}</span>
                    </li>
                @endforeach
            </ul>
        </section>
    </div>
</x-layouts.app>