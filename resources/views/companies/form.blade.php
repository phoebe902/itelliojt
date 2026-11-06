@php
    $editing = $company->exists;
    $field   = 'mt-1 w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-800 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20';
@endphp

<x-layouts.app :title="$editing ? 'Edit partner' : 'Add partner'">
    <div class="max-w-2xl">
        <a href="{{ route('companies.index') }}" class="text-xs font-semibold text-slate-400 hover:text-blue-700">&larr; Partner companies</a>

        <p class="mt-4 text-xs font-semibold uppercase tracking-wide text-blue-600">Industry network</p>
        <h1 class="mt-2 text-4xl font-normal tracking-tight text-slate-900">{{ $editing ? 'Edit partner' : 'Add partner' }}</h1>
        <p class="mt-2 text-sm text-slate-500">Host organizations, their placement capacity, and the primary supervisor.</p>

        <form method="POST" action="{{ $editing ? route('companies.update', $company) : route('companies.store') }}"
              class="mt-8 space-y-5 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            @csrf
            @if ($editing) @method('PUT') @endif

            <label class="block">
                <span class="text-xs font-semibold text-slate-600">Company name</span>
                <input name="name" value="{{ old('name', $company->name) }}" required class="{{ $field }}">
                @error('name') <span class="text-xs text-rose-600">{{ $message }}</span> @enderror
            </label>

            <label class="block">
                <span class="text-xs font-semibold text-slate-600">Industry</span>
                <input name="industry" value="{{ old('industry', $company->industry) }}" list="industries" class="{{ $field }}">
                <datalist id="industries">
                    @foreach ($industries as $industry) <option value="{{ $industry }}"> @endforeach
                </datalist>
                @error('industry') <span class="text-xs text-rose-600">{{ $message }}</span> @enderror
            </label>

            <div class="grid grid-cols-2 gap-4">
                <label class="block">
                    <span class="text-xs font-semibold text-slate-600">Intern capacity (slots)</span>
                    <input type="number" name="capacity" value="{{ old('capacity', $company->capacity) }}" min="0" max="500" required class="{{ $field }}">
                    @error('capacity') <span class="text-xs text-rose-600">{{ $message }}</span> @enderror
                </label>
                <label class="block">
                    <span class="text-xs font-semibold text-slate-600">Rating (0 to 5)</span>
                    <input type="number" step="0.1" name="rating" value="{{ old('rating', $company->rating) }}" min="0" max="5" class="{{ $field }}">
                    @error('rating') <span class="text-xs text-rose-600">{{ $message }}</span> @enderror
                </label>
            </div>

            <label class="block">
                <span class="text-xs font-semibold text-slate-600">Primary supervisor</span>
                <input name="supervisor_name" value="{{ old('supervisor_name', $company->supervisor_name) }}" class="{{ $field }}">
                @error('supervisor_name') <span class="text-xs text-rose-600">{{ $message }}</span> @enderror
            </label>

            <label class="flex items-center gap-2 text-sm text-slate-600">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $company->is_active)) class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                Active partner (shown in the network and available for placements)
            </label>

            <div class="flex justify-end gap-3 pt-2">
                <a href="{{ route('companies.index') }}" class="rounded-xl px-4 py-2.5 text-sm font-medium text-slate-500 hover:text-slate-800">Cancel</a>
                <button class="rounded-xl bg-blue-600 px-6 py-2.5 text-sm font-semibold text-white shadow-lg shadow-blue-600/25 hover:bg-blue-700">
                    {{ $editing ? 'Save changes' : 'Add partner' }}
                </button>
            </div>
        </form>
    </div>
</x-layouts.app>