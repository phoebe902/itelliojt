@php
    $field = 'mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20';
    $storeUrl = route('admin.programs.store');
    $editId   = old('edit_id');
@endphp

<x-layouts.app title="Programs">
    <div x-data="{
            open: @js($errors->any()),
            mode: @js($editId ? 'edit' : 'create'),
            editId: @js($editId),
            action: @js($editId ? route('admin.programs.update', $editId) : $storeUrl),
            form: {
                code: @js(old('code', '')),
                name: @js(old('name', '')),
                department: @js(old('department', '')),
                required_hours: @js(old('required_hours', $defaultHours)),
                is_active: @js((bool) old('is_active', true)),
            },
            create() {
                this.mode = 'create'; this.editId = null; this.action = @js($storeUrl);
                this.form = { code: '', name: '', department: '', required_hours: @js($defaultHours), is_active: true };
                this.open = true;
            },
            edit(p) {
                this.mode = 'edit'; this.editId = p.id; this.action = p.url;
                this.form = { code: p.code, name: p.name, department: p.department ?? '', required_hours: p.required_hours, is_active: p.is_active };
                this.open = true;
            },
         }">

        {{-- Header --}}
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-blue-600">Administration</p>
                <h1 class="mt-2 text-4xl font-normal tracking-tight text-slate-900">Programs</h1>
                <p class="mt-2 text-sm text-slate-500">Degree programs that send students on OJT, and the hours each one requires.</p>
            </div>

            <button type="button" @click="create()"
                    class="rounded-xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-blue-600/25 transition hover:bg-blue-700">
                + Add program
            </button>
        </div>

        @if (session('status'))
            <div class="mt-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('status') }}</div>
        @endif
        @if (session('error'))
            <div class="mt-6 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">{{ session('error') }}</div>
        @endif

        {{-- Table --}}
        <section class="mt-8 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="border-b border-slate-200 bg-slate-50 text-[11px] font-semibold uppercase tracking-wider text-slate-400">
                        <tr>
                            <th class="px-6 py-4">Program</th>
                            <th class="px-6 py-4">Department</th>
                            <th class="px-6 py-4">Required hours</th>
                            <th class="px-6 py-4">Students</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4 text-right">Actions</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">
                        @forelse ($programs as $program)
                            <tr class="transition hover:bg-slate-50/70">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-4">
                                        <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-violet-50 text-xs font-semibold text-violet-700">{{ \Illuminate\Support\Str::limit($program->code, 4, '') }}</span>
                                        <div>
                                            <p class="text-sm font-semibold text-slate-900">{{ $program->name }}</p>
                                            <p class="text-[11px] text-slate-400">{{ $program->code }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-500">{{ $program->department ?? '—' }}</td>
                                <td class="px-6 py-4 text-sm font-semibold text-slate-700">{{ $program->required_hours }} hrs</td>
                                <td class="px-6 py-4 text-sm font-semibold text-slate-700">{{ $program->students_count }}</td>
                                <td class="px-6 py-4">
                                    <span @class([
                                        'inline-block rounded-full px-3 py-1 text-[11px] font-semibold',
                                        'bg-emerald-50 text-emerald-700' => $program->is_active,
                                        'bg-slate-100 text-slate-500'    => ! $program->is_active,
                                    ])>{{ $program->is_active ? 'Active' : 'Inactive' }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-end gap-2">
                                        <button type="button"
                                                @click="edit(@js([
                                                    'id' => $program->id,
                                                    'url' => route('admin.programs.update', $program),
                                                    'code' => $program->code,
                                                    'name' => $program->name,
                                                    'department' => $program->department,
                                                    'required_hours' => $program->required_hours,
                                                    'is_active' => $program->is_active,
                                                ]))"
                                                class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-600 hover:bg-slate-50">Edit</button>

                                        <form method="POST" action="{{ route('admin.programs.destroy', $program) }}"
                                              onsubmit="return confirm('Delete {{ addslashes($program->code) }}?')">
                                            @csrf @method('DELETE')
                                            <button class="rounded-lg px-3 py-1.5 text-xs font-semibold text-rose-600 hover:bg-rose-50">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-6 py-16 text-center text-sm text-slate-400">No programs yet. Add the first one.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        {{-- Create / edit modal (Alpine.js) --}}
        <div x-cloak x-show="open" x-transition.opacity @keydown.escape.window="open = false"
             class="fixed inset-0 z-40 flex items-center justify-center bg-slate-900/40 p-4">
            <div @click.outside="open = false" class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-xl">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-slate-900" x-text="mode === 'edit' ? 'Edit program' : 'Add program'"></h2>
                    <button type="button" @click="open = false" class="text-slate-400 hover:text-slate-600" aria-label="Close">✕</button>
                </div>

                <form method="POST" :action="action" class="mt-5 space-y-4">
                    @csrf
                    <template x-if="mode === 'edit'">
                        <div>
                            <input type="hidden" name="_method" value="PUT">
                            <input type="hidden" name="edit_id" :value="editId">
                        </div>
                    </template>

                    <div class="grid grid-cols-3 gap-4">
                        <label class="col-span-1 block">
                            <span class="text-xs font-semibold text-slate-500">Code</span>
                            <input name="code" x-model="form.code" required placeholder="BSIT" class="{{ $field }} uppercase">
                            @error('code') <span class="text-xs text-rose-600">{{ $message }}</span> @enderror
                        </label>
                        <label class="col-span-2 block">
                            <span class="text-xs font-semibold text-slate-500">Program name</span>
                            <input name="name" x-model="form.name" required class="{{ $field }}">
                            @error('name') <span class="text-xs text-rose-600">{{ $message }}</span> @enderror
                        </label>
                    </div>

                    <label class="block">
                        <span class="text-xs font-semibold text-slate-500">Department (optional)</span>
                        <input name="department" x-model="form.department" class="{{ $field }}">
                        @error('department') <span class="text-xs text-rose-600">{{ $message }}</span> @enderror
                    </label>

                    <label class="block">
                        <span class="text-xs font-semibold text-slate-500">Required OJT hours</span>
                        <input type="number" name="required_hours" x-model="form.required_hours" min="1" max="2000" required class="{{ $field }}">
                        @error('required_hours') <span class="text-xs text-rose-600">{{ $message }}</span> @enderror
                    </label>

                    <label class="flex items-center gap-2 text-sm text-slate-600">
                        <input type="checkbox" name="is_active" value="1" x-model="form.is_active" class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                        Active (available when placing students)
                    </label>

                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" @click="open = false" class="rounded-lg px-4 py-2 text-sm font-medium text-slate-500 hover:text-slate-800">Cancel</button>
                        <button class="rounded-lg bg-blue-600 px-5 py-2 text-sm font-semibold text-white hover:bg-blue-700"
                                x-text="mode === 'edit' ? 'Save changes' : 'Add program'"></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>