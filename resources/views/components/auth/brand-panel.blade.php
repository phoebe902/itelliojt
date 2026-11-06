@php
    $features = [
        ['Stay informed',           'See progress and requirements in one place.'],
        ['Act with confidence',     'Receive clear, data-guided recommendations.'],
        ['Build stronger outcomes', 'Keep academic and workplace goals aligned.'],
    ];

    // Decorative preview card (static illustration, not real data)
    $bars = [28, 36, 30, 40, 44, 54];
    $ring = 2 * M_PI * 22;
@endphp

<aside class="relative hidden overflow-hidden bg-[#06173d] text-white lg:block"
       style="background-image:
                radial-gradient(circle at 78% 58%, rgba(37,99,235,.38), transparent 42%),
                linear-gradient(rgba(255,255,255,.035) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,.035) 1px, transparent 1px);
              background-size: auto, 44px 44px, 44px 44px;">
    <div class="flex h-full min-h-screen flex-col px-[72px] py-12">

        {{-- Top bar --}}
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-gradient-to-br from-blue-500 to-blue-700 shadow-lg shadow-blue-900/50">
                    <svg class="h-6 w-6 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 17l5-5 3 3 6-7" /><path d="M14 8h5v5" />
                    </svg>
                </div>
                <div>
                    <p class="text-lg font-semibold leading-tight">IntelliOJT</p>
                    <p class="text-[10px] text-blue-200/70">Predict. Support. Succeed.</p>
                </div>
            </div>

            <span class="rounded-lg border border-white/15 bg-white/5 px-3.5 py-2 text-[10px] font-semibold uppercase tracking-wider text-blue-100">
                {{ \App\Models\Setting::get('program_name', 'UNM OJT Program') }}
                <p>&copy; {{ date('Y') }} {{ \App\Models\Setting::get('institution', 'University of Northwestern Mindanao') }}. All rights reserved.</p>

            </span>
        </div>

        {{-- Headline + features + preview --}}
        <div class="flex flex-1 flex-col justify-center gap-9 py-10">
            <div>
                <p class="flex items-center gap-3 text-[11px] font-semibold uppercase tracking-[0.18em] text-teal-300">
                    <span class="h-px w-8 bg-teal-300"></span>Intelligent internship management
                </p>
                <h2 class="mt-6 max-w-lg text-[56px] font-semibold leading-[1.08] tracking-tight">
                    Turn every OJT journey into a success story.
                </h2>
                <p class="mt-6 max-w-lg text-[15px] leading-relaxed text-blue-100/80">
                    Join a connected platform that brings students, coordinators, and industry partners together through timely insights and meaningful support.
                </p>
            </div>

            <ul class="grid max-w-xl grid-cols-3 gap-6">
                @foreach ($features as [$title, $text])
                    <li class="flex gap-2.5">
                        <span class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full border border-teal-300/30 bg-teal-300/10 text-teal-300">
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 13l4 4L19 7" /></svg>
                        </span>
                        <span>
                            <span class="block text-xs font-semibold">{{ $title }}</span>
                            <span class="mt-1 block text-[10px] leading-snug text-blue-200/70">{{ $text }}</span>
                        </span>
                    </li>
                @endforeach
            </ul>

            {{-- Preview cards --}}
            <div class="relative h-44 max-w-[34rem]">
                <div class="absolute left-0 top-0 h-36 w-[26rem] rounded-2xl border border-white/10 bg-white/[0.06] p-5 backdrop-blur">
                    <div class="flex items-center justify-between text-[10px]">
                        <span class="text-xs font-semibold">Intern progress</span>
                        <span class="text-blue-200/60">Live overview</span>
                    </div>

                    <div class="mt-4 flex items-center gap-3">
                        <div class="relative h-12 w-12">
                            <svg viewBox="0 0 56 56" class="h-full w-full -rotate-90">
                                <circle cx="28" cy="28" r="22" fill="none" class="stroke-white/15" stroke-width="5" />
                                <circle cx="28" cy="28" r="22" fill="none" class="stroke-teal-300" stroke-width="5" stroke-linecap="round"
                                        stroke-dasharray="{{ round($ring * .86, 2) }} {{ round($ring * .14, 2) }}" />
                            </svg>
                            <span class="absolute inset-0 flex items-center justify-center text-[11px] font-semibold">86%</span>
                        </div>
                        <div>
                            <p class="text-xs font-semibold">Excellent momentum</p>
                            <p class="text-[10px] text-blue-200/70">On track for completion</p>
                        </div>
                    </div>

                    <div class="absolute bottom-5 right-6 flex h-14 items-end gap-2">
                        @foreach ($bars as $h)
                            <span class="w-5 rounded-t bg-gradient-to-t from-blue-600 to-blue-400" style="height: {{ $h }}px"></span>
                        @endforeach
                    </div>
                </div>

                <div class="absolute bottom-0 left-[19rem] w-44 rounded-xl border border-white/15 bg-[#0d2a66] p-3 shadow-xl shadow-black/30">
                    <div class="flex items-center gap-2.5">
                        <span class="flex h-6 w-6 items-center justify-center rounded-full bg-blue-500/30">
                            <span class="h-2 w-2 rounded-full bg-teal-300"></span>
                        </span>
                        <span>
                            <span class="block text-[9px] text-blue-200/70">Predictive insight</span>
                            <span class="block text-xs font-semibold">Progress improving</span>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <p class="text-[10px] text-blue-200/50">&copy; {{ now()->year }} University of Northwestern Mindanao</p>
    </div>
</aside>