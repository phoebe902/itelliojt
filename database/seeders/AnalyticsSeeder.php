<?php

namespace Database\Seeders;

use App\Enums\RiskLevel;
use App\Models\Competency;
use App\Models\ForecastSnapshot;
use App\Models\Intervention;
use App\Models\RiskSignal;
use App\Models\Student;
use Illuminate\Database\Seeder;

class AnalyticsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $students = Student::all();

        ForecastSnapshot::create([
            'success_rate' => 92,
            'model_confidence' => 94, 
            'calculated_at' => now()->subMinutes(14),
        ]);
    

    foreach([
        ['Technical proficiency', 82, 88],
        ['Communication',         80, 76],
        ['Task management',       79, 71],
        ['Problem solving',       81, 84],
        ['Professional conduct',  85, 92],
    ]as $i =>[$name, $benchmark, $average]){
        $competency = Competency::create([
            'name' => $name,
            'benchmark' => $benchmark,
            'sort_order' => $i,
        ]);

         $competency->students()->attach(
                $students->mapWithKeys(fn ($s) => [
                    $s->id => ['score' => max(40, min(100, $average + random_int(-10, 10)))],
                ])->all()
            );
        }

        $atRisk = $students->filter(fn ($s) => $s->risk_level !== RiskLevel::OnTrack);

        foreach ([
            ['Attendance below 85%',   60],
            ['Declining task velocity', 45],
            ['Low supervisor rating',   30],
        ] as [$label, $chance]) {
            $signal = RiskSignal::create(['label' => $label]);
            $signal->students()->attach(
                $atRisk->filter(fn () => random_int(1, 100) <= $chance)->pluck('id')->values()->all()
            );
        }

        // 150 interventions over the past year; recent ones succeed more often
        $types = ['Mentoring', 'Schedule change', 'Supervisor check-in', 'Skills workshop'];
        $rows  = [];

        for ($n = 0; $n < 150; $n++) {
            $daysAgo = random_int(0, 365);
            $chance  = 55 + (int) (25 * (1 - $daysAgo / 365));

            $rows[] = [
                'student_id'    => $students->random()->id,
                'type'          => $types[array_rand($types)],
                'improved'      => random_int(1, 100) <= $chance,
                'intervened_on' => now()->subDays($daysAgo)->toDateString(),
                'created_at'    => now(),
                'updated_at'    => now(),
            ];
        }

        Intervention::insert($rows);
    }
}