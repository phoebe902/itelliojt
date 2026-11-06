<?php

namespace App\Http\Controllers;

use App\Enums\RiskLevel;
use App\Models\Company;
use App\Models\MetricSnapshot;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DashboardController extends Controller
{
    // months to look back, wording for the cards, number of chart points
    private const PERIODS = [
        'month'    => ['months' => 1,  'label' => 'this month',    'noun' => 'month',    'points' => 4],
        'semester' => ['months' => 6,  'label' => 'this semester', 'noun' => 'semester', 'points' => 6],
        'year'     => ['months' => 12, 'label' => 'this year',     'noun' => 'year',     'points' => 12],
    ];

    public function index(Request $request)
    {
        $key    = array_key_exists($request->query('period'), self::PERIODS) ? $request->query('period') : 'semester';
        $period = self::PERIODS[$key];
        $since  = now()->subMonths($period['months']);

        // ---- Greeting ----
        $hour     = now()->hour;
        $greeting = $hour < 12 ? 'Good morning' : ($hour < 18 ? 'Good afternoon' : 'Good evening');
        $name     = Str::of(auth()->user()?->name ?? '')->trim()->before(' ')->toString() ?: 'there';

        // ---- Risk counts (one query, reused by the cards and the donut) ----
        $counts   = Student::query()->selectRaw('risk_level, count(*) as total')->groupBy('risk_level')->pluck('total', 'risk_level');
        $total    = (int) $counts->sum();
        $high     = (int) ($counts['high'] ?? 0);
        $atRisk   = (int) ($counts['medium'] ?? 0) + $high;

        // ---- Completion, compared with the same point one period ago ----
        $completion = Student::averageCompletion();
        $previous   = MetricSnapshot::metric('completion')
            ->where('recorded_on', '<=', $since->toDateString())
            ->orderByDesc('recorded_on')
            ->value('value');
        $delta = $previous !== null ? $completion - $previous : null;

        // ---- Stat cards ----
        $partners = Company::where('is_active', true);

        $stats = [
            [
                'label' => 'Active Interns', 'value' => $total, 'icon' => 'users',
                'trend' => 'up', 'good' => true,
                'note'  => Student::where('created_at', '>=', $since)->count() . ' new ' . $period['label'],
            ],
            [
                'label' => 'Partner Companies', 'value' => (clone $partners)->count(), 'icon' => 'building',
                'trend' => 'up', 'good' => true,
                'note'  => (clone $partners)->where('created_at', '>=', $since)->count() . ' added ' . $period['label'],
            ],
            [
                'label' => 'At-Risk Students', 'value' => $atRisk, 'icon' => 'alert',
                'trend' => 'down', 'good' => false,
                'note'  => $high . ' require attention',
            ],
            [
                'label' => 'Avg. Completion', 'value' => round($completion) . '%', 'icon' => 'check',
                'trend' => ($delta ?? 0) >= 0 ? 'up' : 'down', 'good' => ($delta ?? 0) >= 0,
                'note'  => $delta === null ? null : abs(round($delta, 1)) . '% vs last ' . $period['noun'],
            ],
        ];

        // ---- Performance line chart: newest N weekly snapshots, oldest first ----
        $performance = MetricSnapshot::metric('performance')
            ->orderByDesc('recorded_on')
            ->take($period['points'])
            ->get()
            ->reverse()
            ->values()
            ->map(fn ($s) => ['label' => $s->recorded_on->format('M j'), 'score' => $s->value])
            ->all();

        // ---- Risk donut ----
        $risk = collect(RiskLevel::cases())->map(fn (RiskLevel $level) => [
            'level' => $level,
            'count' => (int) ($counts[$level->value] ?? 0),
        ])->all();

        return view('dashboard', [
            'greeting'    => $greeting,
            'name'        => $name,
            'periods'     => ['semester' => 'This semester', 'month' => 'This month', 'year' => 'This year'],
            'period'      => $key,
            'stats'       => $stats,
            'performance' => $performance,
            'risk'        => $risk,
            'total'       => $total,
        ]);
    }
}