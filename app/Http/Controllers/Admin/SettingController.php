<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SettingController extends Controller
{
    public function edit()
    {
        $groups = config('ojt_settings');
        $values = [];

        foreach ($groups as $fields) {
            foreach ($fields as $key => $field) {
                $values[$key] = Setting::get($key, $field['default'] ?? null);
            }
        }

        return view('admin.settings.edit', compact('groups', 'values'));
    }

    public function update(Request $request)
    {
        $fields = collect(config('ojt_settings'))->collapse();   // key => definition

        $rules = $fields->map(fn ($f) => match ($f['type']) {
            'email'  => ['nullable', 'email', 'max:150'],
            'number' => ['required', 'integer', 'min:' . ($f['min'] ?? 0), 'max:' . ($f['max'] ?? 100000)],
            'select' => ['required', Rule::in(array_keys($f['options']))],
            'toggle' => ['boolean'],
            default  => ['nullable', 'string', 'max:150'],
        })->all();

        $data = $request->validate($rules);

        foreach ($fields as $key => $field) {
            $value = $field['type'] === 'toggle'
                ? ($request->boolean($key) ? '1' : '0')
                : ($data[$key] ?? null);

            Setting::set($key, $value);
        }

        return back()->with('status', 'Settings saved.');
    }
}