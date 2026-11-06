@props(['roles', 'selected' => null, 'legend' => 'Account type'])

<fieldset>
    <legend class="mb-2 block text-sm font-medium text-slate-700">{{ $legend }}</legend>

    <div @class(['grid gap-3', 'grid-cols-2' => count($roles) <= 2, 'grid-cols-2 sm:grid-cols-3' => count($roles) > 2])>
        @foreach ($roles as $role)
            <label class="cursor-pointer">
                <input type="radio" name="role" value="{{ $role->value }}" required
                       @checked($selected === $role->value) class="peer sr-only">

                <span class="flex h-full flex-col items-center gap-2 rounded-xl border border-slate-200 bg-slate-50 px-3 py-4 text-center text-sm font-medium text-slate-500 transition hover:bg-white peer-checked:border-blue-600 peer-checked:bg-blue-50 peer-checked:text-blue-700 peer-focus-visible:ring-4 peer-focus-visible:ring-blue-500/20">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                        <path d="{{ $role->icon() }}" />
                    </svg>
                    {{ $role->label() }}
                </span>
            </label>
        @endforeach
    </div>
</fieldset>