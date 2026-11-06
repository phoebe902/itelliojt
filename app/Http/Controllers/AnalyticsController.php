<?php

namespace App\Http\Controllers;

use App\Models\Competency;
use App\Models\ForecastSnapshot;
use App\Models\Intervention;
use App\Models\RiskSignal;
use App\Models\Student;

class AnalyticsController extends Controller
{
    public function index()
    {
        // Hero forecast: newest snapshot written by the prediction job
        $snapshot = ForecastSnapshot::latest('calculated_at')->first();
        $forecast = [
            'success'    => $snapshot?->success_rate ?? 0,
            'confidence' => $snapshot?->model_confidence ?? 0,
            'calculated' => $snapshot?->calculated_at?->diffForHumans() ?? 'never',
        ];

        // Competency performance: cohort average vs benchmark
        $competencies = Competency::query()
            ->withAvg('students as cohort_score', 'competency_student.score')
            ->orderBy('sort_order')
            ->get();

        // Top risk signals among medium/high-risk students
        $atRiskCount = Student::whereIn('risk_level', ['medium', 'high'])->count();

        $signals = RiskSignal::query()
            ->withCount(['students' => fn ($q) => $q->whereIn('risk_level', ['medium', 'high'])])
            ->orderByDesc('students_count')
            ->take(3)
            ->get();

        // Intervention impact: "semester" = last 6 months
        $now      = now();
        $midpoint = $now->copy()->subMonths(6);

        $rate = fn ($from, $to) => 100 * (float) Intervention::whereBetween('intervened_on', [$from, $to])->avg('improved');

        $current  = $rate($midpoint, $now);
        $previous = $rate($now->copy()->subMonths(12), $midpoint);

        $monthly = Intervention::query()
            ->where('intervened_on', '>=', $now->copy()->startOfMonth()->subMonths(6))
            ->selectRaw("DATE_FORMAT(intervened_on, '%Y-%m') as ym, AVG(improved) * 100 as rate")
            ->groupBy('ym')
            ->pluck('rate', 'ym');

        $bars = collect(range(6, 0))->map(function ($i) use ($monthly, $now) {
            $month = $now->copy()->startOfMonth()->subMonths($i);

            return [
                'label' => $month->format('M'),
                'rate'  => (float) ($monthly[$month->format('Y-m')] ?? 0),
            ];
        });

        return view('analytics.index', [
            'forecast'     => $forecast,
            'competencies' => $competencies,
            'signals'      => $signals,
            'atRiskCount'  => $atRiskCount,
            'impact'       => [
                'current' => $current,
                'delta'   => $current - $previous,
                'bars'    => $bars,
            ],
        ]);
    }
}