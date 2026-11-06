<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Program;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProgramController extends Controller
{
    public function index()
    {
        return view('admin.programs.index', [
            'programs'     => Program::withCount('students')->orderBy('code')->get(),
            'defaultHours' => (int) Setting::get('default_hours', 486),
        ]);
    }

    public function store(Request $request)
    {
        Program::create($this->validated($request));

        return back()->with('status', 'Program added.');
    }

    public function update(Request $request, Program $program)
    {
        $program->update($this->validated($request, $program));

        return back()->with('status', "{$program->code} was updated.");
    }

    public function destroy(Program $program)
    {
        if ($program->students()->exists()) {
            return back()->with('error', "{$program->code} still has students. Reassign them first, or mark the program inactive instead.");
        }

        $program->delete();

        return back()->with('status', "{$program->code} was deleted.");
    }

    private function validated(Request $request, ?Program $program = null): array
    {
        $request->merge(['code' => strtoupper(trim((string) $request->input('code')))]);

        $data = $request->validate([
            'code'           => ['required', 'string', 'max:20', Rule::unique('programs', 'code')->ignore($program)],
            'name'           => ['required', 'string', 'max:120'],
            'department'     => ['nullable', 'string', 'max:120'],
            'required_hours' => ['required', 'integer', 'min:1', 'max:2000'],
        ]);

        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}