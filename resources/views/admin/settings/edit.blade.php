@php
    $field = 'mt-1 w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20';
@endphp

<x-layouts.app title="Settings">
    <div class="max-w-3xl">
        <p class="text-xs font-semibold uppercase tracking-wide text-blue-600">Administration</p>
        <h1 class="mt-2 text-4xl font-normal tracking-tight text-slate-900">System settings</h1>
        <p class="mt-2 text-sm text-slate-500">Program-wide defaults and how people can join IntelliOJT.</p>

        @if (session('status'))
            <div class="mt-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('status') }}</div>
        @endif

        <form method="POST" action="{{ route('admin.settings.update') }}" class="mt-8 space-y-6">
            @csrf @method('PUT')

            @foreach ($groups as $group => $fields)
                <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h2 class="text-base font-semibold text-slate-900">{{ $group }}</h2>

                    <div class="mt-5 space-y-5">
                        @foreach ($fields as $key => $f)
                            @php $value = old($key, $values[$key]); @endphp

                            @if ($f['type'] === 'toggle')
                                <label class="flex cursor-pointer items-start justify-between gap-6">
                                    <span>
                                        <span class="block text-sm font-semibold text-slate-800">{{ $f['label'] }}</span>
                                        @isset($f['help']) <span class="mt-0.5 block text-xs text-slate-400">{{ $f['help'] }}</span> @endisset
                                    </span>
                                    <span class="relative mt-0.5 shrink-0">
                                        <input type="hidden" name="{{ $key }}" value="0">
                                        <input type="checkbox" name="{{ $key }}" value="1" @checked(filter_var($value, FILTER_VALIDATE_BOOLEAN)) class="peer sr-only">
                                        <span class="block h-6 w-11 rounded-full bg-slate-200 transition peer-checked:bg-blue-600 peer-focus-visible:ring-2 peer-focus-visible:ring-blue-600 peer-focus-visible:ring-offset-2"></span>
                                        <span class="absolute left-0.5 top-0.5 h-5 w-5 rounded-full bg-white shadow transition peer-checked:translate-x-5"></span>
                                    </span>
                                </label>
                            @else
                                <label class="block">
                                    <span class="text-xs font-semibold text-slate-600">{{ $f['label'] }}</span>

                                    @if ($f['type'] === 'select')
                                        <select name="{{ $key }}" class="{{ $field }}">
                                            @foreach ($f['options'] as $optValue => $optLabel)
                                                <option value="{{ $optValue }}" @selected((string) $value === (string) $optValue)>{{ $optLabel }}</option>
                                            @endforeach
                                        </select>
                                    @else
                                        <input type="{{ $f['type'] }}" name="{{ $key }}" value="{{ $value }}"
                                               @isset($f['min']) min="{{ $f['min'] }}" @endisset
                                               @isset($f['max']) max="{{ $f['max'] }}" @endisset
                                               class="{{ $field }}">
                                    @endif

                                    @isset($f['help']) <span class="mt-1 block text-[11px] text-slate-400">{{ $f['help'] }}</span> @endisset
                                    @error($key) <span class="mt-1 block text-xs text-rose-600">{{ $message }}</span> @enderror
                                </label>
                            @endif
                        @endforeach
                    </div>
                </section>
            @endforeach

            <div class="flex justify-end">
                <button class="rounded-xl bg-blue-600 px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-blue-600/25 hover:bg-blue-700">
                    Save changes
                </button>
            </div>
        </form>
    </div>
</x-layouts.app>