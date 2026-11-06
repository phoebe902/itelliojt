<header class="sticky top-0 z-10 flex h-[86px] items-center justify-between gap-6 border-b border-slate-200 bg-white px-8">
    {{-- Search --}}
    <div class="relative w-full max-w-md">
        <svg class="pointer-events-none absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
        </svg>
        <input type="search" placeholder="Search students, companies..."
               class="w-full rounded-xl border border-slate-200 bg-slate-50/50 py-3 pl-12 pr-16 text-sm text-slate-700 placeholder-slate-400 focus:border-blue-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-blue-500/20">
        <kbd class="absolute right-3 top-1/2 -translate-y-1/2 rounded-md border border-slate-200 bg-white px-2 py-0.5 text-[11px] font-medium text-slate-400">⌘ K</kbd>
    </div>

    {{-- Right side --}}
    <div class="flex items-center gap-6">
        
         <form action="{{ route('logout') }}" method="POST">
            @csrf
           <button class="flex w-full items-center justify-center rounded-xl bg-red-600 px-4 py-3.5 text-sm font-semibold text-white shadow-lg shadow-blue-600/20 transition hover:-translate-y-0.5 hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-500/30" type=submit>Logout
            </button>
        </form>
        <a href="#" class="text-sm text-slate-500 hover:text-slate-800">Help &amp; support</a>

        <button type="button" class="relative text-slate-500 hover:text-slate-800" aria-label="Notifications">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                <path d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
            </svg>
            <span class="absolute right-0 top-0 h-2 w-2 rounded-full bg-red-500 ring-2 ring-white"></span>
        </button>

        <div x-data="{ open: false }" class="relative">
    <button type="button" @click="open = !open"
            class="flex h-11 w-11 items-center justify-center rounded-full bg-gradient-to-br from-blue-600 to-indigo-600 text-sm font-semibold text-white">
        {{ auth()->user()?->initials ?? 'ME' }}
    </button>

    <div x-cloak x-show="open" x-transition.opacity @click.outside="open = false"
         class="absolute right-0 z-20 mt-2 w-60 rounded-xl border border-slate-200 bg-white py-2 shadow-lg">
        <div class="border-b border-slate-100 px-4 pb-3">
            <p class="text-sm font-semibold text-slate-800">{{ auth()->user()?->full_name ?? 'Guest' }}</p>
            <p class="text-xs text-slate-400">{{ auth()->user()?->role?->label() ?? 'Not signed in' }}</p>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="block w-full px-4 py-2 text-left text-sm text-slate-600 hover:bg-slate-50">Sign out</button>
        </form>
    </div>
</div>
    </div>
</header>