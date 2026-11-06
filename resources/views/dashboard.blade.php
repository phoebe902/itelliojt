@php
    $icons = [
        'users'    => 'M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z',
        'building' => 'M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21',
        'alert'    => 'M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z',
        'check'    => 'M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        'up'       => 'M4.5 10.5L12 3m0 0l7.5 7.5M12 3v18',
        'down'     => 'M19.5 13.5L12 21m0 0l-7.5-7.5M12 21V3',
    ];

    // Tailwind needs full class names, so these are written out literally
    $tones = [
        'users'    => 'bg-blue-50 text-blue-600',
        'building' => 'bg-violet-50 text-violet-600',
        'alert'    => 'bg-rose-50 text-rose-500',
        'check'    => 'bg-emerald-50 text-emerald-600',
    ];

    $strokes = [
        'on_track' => 'stroke-emerald-500',
        'medium'   => 'stroke-amber-400',
        'high'     => 'stroke-rose-500',
    ];

    // ---- Line chart geometry ----
    $W = 600; $H = 280;
    $padL = 40; $padR = 16; $padT = 12; $padB = 32;
    $cw = $W - $padL - $padR;
    $ch = $H - $padT - $padB;
    $n  = count($performance);

    $points = [];
    foreach ($performance as $i => $p) {
        $points[] = [
            'x'     => $padL + ($n > 1 ? $i * $cw / ($n - 1) : $cw / 2),
            'y'     => $padT + (1 - min(100, max(0, $p['score'])) / 100) * $ch,
            'label' => $p['label'],
            'score' => $p['score'],
        ];
    }

    $line = $area = '';
    if ($n > 1) {
        // Smooth curve using horizontal control points
        $line = 'M' . round($points[0]['x'], 1) . ',' . round($points[0]['y'], 1);
        for ($i = 1; $i < $n; $i++) {
            $mx = ($points[$i - 1]['x'] + $points[$i]['x']) / 2;
            $line .= ' C' . round($mx, 1) . ',' . round($points[$i - 1]['y'], 1)
                   . ' ' . round($mx, 1) . ',' . round($points[$i]['y'], 1)
                   . ' ' . round($points[$i]['x'], 1) . ',' . round($points[$i]['y'], 1);
        }
        $baseY = $padT + $ch;
        $area  = $line . ' L' . round($points[$n - 1]['x'], 1) . ',' . $baseY
               . ' L' . round($points[0]['x'], 1) . ',' . $baseY . ' Z';
    }

    // ---- Donut geometry ----
    $radius = 80;
    $circ   = 2 * M_PI * $radius;
    $offset = 0;
@endphp

<x-layouts.app title="Overview">

    {{-- Greeting --}}
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wide text-blue-600">{{ now()->format('l, F j') }}</p>
            <h1 class="mt-2 text-4xl font-normal tracking-tight text-slate-900">{{ $greeting }}, {{ $name }}.</h1>
            <p class="mt-2 text-sm text-slate-500">Here's what's happening with your OJT program today.</p>
        </div>

        <form method="GET">
            <select name="period" onchange="this.form.submit()"
                    class="rounded-xl border border-slate-200 bg-white px-4 py-3 pr-10 text-sm text-slate-600 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20">
                @foreach ($periods as $value => $label)
                    <option value="{{ $value }}" @selected($period === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </form>
    </div>

    {{-- Stat cards --}}
    <div class="mt-8 grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ($stats as $s)
            <div class="flex items-center gap-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl {{ $tones[$s['icon']] }}">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                        <path d="{{ $icons[$s['icon']] }}" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-slate-500">{{ $s['label'] }}</p>
                    <p class="text-3xl font-semibold leading-tight text-slate-900">{{ $s['value'] }}</p>

                    @if ($s['note'])
                        <p @class([
                            'mt-0.5 flex items-center gap-1 text-xs',
                            'text-emerald-600' => $s['good'],
                            'text-rose-500'    => ! $s['good'],
                        ])>
                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="{{ $icons[$s['trend']] }}" />
                            </svg>
                            {{ $s['note'] }}
                        </p>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    {{-- Charts --}}
    <div class="mt-6 grid grid-cols-1 gap-6 xl:grid-cols-3">

        {{-- Performance overview --}}
        <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm xl:col-span-2">
            <header class="flex items-start justify-between">
                <div>
                    <h2 class="text-base font-semibold text-slate-900">Performance overview</h2>
                    <p class="mt-1 text-xs text-slate-400">Average intern performance across key metrics</p>
                </div>
                <button type="button" class="text-slate-400 hover:text-slate-600" aria-label="More options">
                    <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                        <circle cx="5" cy="12" r="1.6" /><circle cx="12" cy="12" r="1.6" /><circle cx="19" cy="12" r="1.6" />
                    </svg>
                </button>
            </header>

            @if ($n === 0)
                <p class="py-24 text-center text-sm text-slate-400">
                    No performance data yet. Run <code class="rounded bg-slate-100 px-1.5 py-0.5">php artisan ojt:snapshot</code>.
                </p>
            @else
                <svg viewBox="0 0 {{ $W }} {{ $H }}" class="mt-6 h-auto w-full" role="img" aria-label="Average intern performance by week">
                    <defs>
                        <linearGradient id="perfFill" x1="0" y1="0" x2="0" y2="1">
                            <stop offset="0%" stop-color="#2563eb" stop-opacity="0.22" />
                            <stop offset="100%" stop-color="#2563eb" stop-opacity="0" />
                        </linearGradient>
                    </defs>

                    {{-- Grid + y labels --}}
                    @foreach ([0, 25, 50, 75, 100] as $tick)
                        @php $gy = $padT + (1 - $tick / 100) * $ch; @endphp
                        <line x1="{{ $padL }}" x2="{{ $W - $padR }}" y1="{{ $gy }}" y2="{{ $gy }}"
                              class="stroke-slate-200" stroke-dasharray="3 4" />
                        <text x="{{ $padL - 10 }}" y="{{ $gy + 4 }}" text-anchor="end" class="fill-slate-400" font-size="11">{{ $tick }}</text>
                    @endforeach

                    {{-- Area + line --}}
                    @if ($n > 1)
                        <path d="{{ $area }}" fill="url(#perfFill)" />
                        <path d="{{ $line }}" fill="none" class="stroke-blue-600" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
                    @endif

                    {{-- Points + x labels --}}
                    @foreach ($points as $pt)
                        <circle cx="{{ round($pt['x'], 1) }}" cy="{{ round($pt['y'], 1) }}" r="5" fill="#fff" class="stroke-blue-600" stroke-width="3">
                            <title>{{ $pt['label'] }}: {{ round($pt['score']) }}</title>
                        </circle>
                        <text x="{{ round($pt['x'], 1) }}" y="{{ $H - 8 }}" text-anchor="middle" class="fill-slate-400" font-size="11">{{ $pt['label'] }}</text>
                    @endforeach
                </svg>
            @endif
        </section>

        {{-- Risk distribution --}}
        <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <header class="flex items-start justify-between">
                <div>
                    <h2 class="text-base font-semibold text-slate-900">Risk distribution</h2>
                    <p class="mt-1 text-xs text-slate-400">Current student risk levels</p>
                </div>
                <button type="button" class="text-slate-400 hover:text-slate-600" aria-label="More options">
                    <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                        <circle cx="5" cy="12" r="1.6" /><circle cx="12" cy="12" r="1.6" /><circle cx="19" cy="12" r="1.6" />
                    </svg>
                </button>
            </header>

            <div class="relative mx-auto mt-6 h-52 w-52">
                <svg viewBox="0 0 200 200" class="h-full w-full -rotate-90">
                    <circle cx="100" cy="100" r="{{ $radius }}" fill="none" class="stroke-slate-100" stroke-width="26" />
                    @foreach ($risk as $r)
                        @php $len = $total > 0 ? ($r['count'] / $total) * $circ : 0; @endphp
                        @if ($len > 0)
                            <circle cx="100" cy="100" r="{{ $radius }}" fill="none"
                                    class="{{ $strokes[$r['level']->value] }}" stroke-width="26"
                                    stroke-dasharray="{{ round($len, 2) }} {{ round($circ - $len, 2) }}"
                                    stroke-dashoffset="{{ -round($offset, 2) }}" />
                        @endif
                        @php $offset += $len; @endphp
                    @endforeach
                </svg>
                <div class="absolute inset-0 flex flex-col items-center justify-center">
                    <span class="text-3xl font-semibold text-slate-900">{{ $total }}</span>
                    <span class="text-xs text-slate-400">Total interns</span>
                </div>
            </div>

            <ul class="mt-6 space-y-3">
                @foreach ($risk as $r)
                    <li class="flex items-center justify-between text-sm">
                        <span class="flex items-center gap-2 text-slate-500">
                            <span class="h-2.5 w-2.5 rounded-full {{ $r['level']->dot() }}"></span>
                            {{ $r['level']->label() }}
                        </span>
                        <span class="font-semibold text-slate-900">
                            {{ $r['count'] }}
                            <span class="ml-1 text-xs font-normal text-slate-400">{{ $total ? number_format($r['count'] / $total * 100, 1) : 0 }}%</span>
                        </span>
                    </li>
                @endforeach
            </ul>
        </section>
    </div>
</x-layouts.app>