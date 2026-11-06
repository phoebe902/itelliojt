@php
    $icons = [
        'overview'    => 'M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z',
        'students'    => 'M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z',
        'companies'   => 'M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21',
        'analytics'   => 'M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z',
        'evaluations' => 'M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
        'schedule'    => 'M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5',
        'sparkles'    => 'M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z',
        'programs' => 'M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5',
        'settings' => 'M10.5 6h9.75M10.5 6a1.5 1.5 0 11-3 0m3 0a1.5 1.5 0 10-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-9.75 0h9.75',
    ];

    $sections = [
        'Workspace' => [
            ['label' => 'Overview',    'route' => 'dashboard',          'icon' => 'overview'],
            ['label' => 'Students',    'route' => 'students.index',     'icon' => 'students'],
            ['label' => 'Companies',   'route' => 'companies.index',    'icon' => 'companies'],
            ['label' => 'Analytics',   'route' => 'analytics.index',    'icon' => 'analytics'],
    

        ],
        'Manage' => [
            ['label' => 'Evaluations', 'route' => 'evaluations.index', 'icon' => 'evaluations', 'badge' => \App\Models\Evaluation::awaitingReview()->count()],
            ['label' => 'Schedule',    'route' => 'schedule.index',     'icon' => 'schedule'],
        ],
    ];
    if (auth()->user()?->role === \App\Enums\UserRole::Administrator) {
    $pendingUsers = \App\Models\User::where('status', 'pending')->count();

    $sections['Administration'] = [
        ['label' => 'Users',    'route' => 'admin.users.index',    'icon' => 'students', 'badge' => $pendingUsers ?: null],
        ['label' => 'Programs', 'route' => 'admin.programs.index', 'icon' => 'programs'],
        ['label' => 'Settings', 'route' => 'admin.settings.edit',  'icon' => 'settings'],
    ];
}
@endphp

<aside class="hidden lg:flex lg:w-72 shrink-0 flex-col border-r border-slate-200 bg-white">
    {{-- Brand --}}
    <div class="flex items-center gap-3 px-8 pt-8 pb-6">
        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-600 shadow-lg shadow-blue-600/30">
            <svg class="h-6 w-6 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M4 17l5-5 3 3 6-7" />
                <path d="M14 8h5v5" />
            </svg>
        </div>
        <div>
            <p class="text-xl font-semibold leading-tight text-slate-900">IntelliOJT</p>
            <p class="text-[11px] text-slate-400">Predict. Support. Succeed.</p>
        </div>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 overflow-y-auto px-5">
        @foreach ($sections as $title => $items)
            <p class="mb-3 mt-6 px-3 text-xs font-semibold uppercase tracking-wider text-slate-400">{{ $title }}</p>
            <ul class="space-y-1">
                @foreach ($items as $item)
                    @php $active = request()->routeIs($item['route'] . '*'); @endphp
                    <li>
                        <a href="{{ Route::has($item['route']) ? route($item['route']) : '#' }}"
                           @class([
                               'group relative flex items-center gap-3 rounded-xl px-4 py-3 text-[15px] font-medium transition',
                               'bg-blue-50 text-blue-700' => $active,
                               'text-slate-500 hover:bg-slate-50 hover:text-slate-800' => ! $active,
                           ])>
                            @if ($active)
                                <span class="absolute -left-5 top-1/2 h-8 w-1 -translate-y-1/2 rounded-r-full bg-blue-600"></span>
                            @endif

                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                                <path d="{{ $icons[$item['icon']] }}" />
                            </svg>

                            <span class="flex-1">{{ $item['label'] }}</span>

                            @if (! empty($item['badge']))
    <span class="rounded-full bg-blue-100 px-2 py-0.5 text-xs font-semibold text-blue-700">{{ $item['badge'] }}</span>
@endif
                        </a>
                    </li>
                @endforeach
            </ul>
        @endforeach
    </nav>

    {{-- Predictive insight card --}}
    <div class="m-5 rounded-2xl border border-blue-100 bg-blue-50/60 p-5">
        <div class="mb-3 flex h-9 w-9 items-center justify-center rounded-lg bg-blue-600 text-white">
            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                <path d="{{ $icons['sparkles'] }}" />
            </svg>
           
        </div>
        <p class="font-semibold text-slate-900">Predictive insight</p>
        <p class="mt-1 text-sm text-slate-500">3 students need early intervention this week.</p>
        <a href="#" class="mt-3 inline-flex items-center gap-1 text-sm font-semibold text-blue-700 hover:text-blue-800">
            View analysis
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
        </a>
    </div>
</aside>