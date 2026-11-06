<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Enums\RiskLevel;
use App\Models\Student;

class StudentController extends Controller
{
    // Whitelisted sort options (label shown in UI + SQL expression)
    private const SORTS = [
        'risk'     => ['label' => 'Risk level',     'sql' => "CASE risk_level WHEN 'high' THEN 0 WHEN 'medium' THEN 1 ELSE 2 END"],
        'name'     => ['label' => 'Name',           'sql' => 'last_name'],
        'progress' => ['label' => 'OJT progress',   'sql' => 'rendered_hours / NULLIF(required_hours, 0)'],
        'hours'    => ['label' => 'Hours rendered', 'sql' => 'rendered_hours'],
    ];

    public function index(Request $request)
    {
        $risk   = RiskLevel::tryFrom((string) $request->query('risk'));
        $search = trim((string) $request->query('search'));
        $sort   = array_key_exists($request->query('sort'), self::SORTS) ? $request->query('sort') : 'risk';
        $dir    = $request->query('dir') === 'desc' ? 'desc' : 'asc';

        $students = Student::query()
            ->with('company')
            ->search($search)
            ->when($risk, fn ($q) => $q->where('risk_level', $risk))
            ->orderByRaw(self::SORTS[$sort]['sql'] . ' ' . $dir)
            ->orderBy('last_name')
            ->paginate(10)
            ->withQueryString();

        // Counts for the summary strip + tabs, one query
        $counts = Student::query()
            ->selectRaw('risk_level, count(*) as total')
            ->groupBy('risk_level')
            ->pluck('total', 'risk_level');

        return view('students.index', [
            'students' => $students,
            'counts'   => $counts,
            'total'    => $counts->sum(),
            'risk'     => $risk,
            'search'   => $search,
            'sort'     => $sort,
            'dir'      => $dir,
            'sorts'    => collect(self::SORTS)->pluck('label'),
        ]);
    }
}
