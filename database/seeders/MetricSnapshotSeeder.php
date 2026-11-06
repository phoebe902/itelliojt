<?php

namespace Database\Seeders;

use App\Models\MetricSnapshot;
use App\Models\Student;
use Illuminate\Database\Seeder;

class MetricSnapshotSeeder extends Seeder
{
    public function run(): void
    {
        MetricSnapshot::query()->delete();   // safe to re-run

        // 12 weekly performance points, rising from ~31 to 84 (like the design)
        for ($i = 11; $i >= 0; $i--) {
            MetricSnapshot::create([
                'metric'      => 'performance',
                'value'       => round(84 - $i * 4.8, 1),
                'recorded_on' => today()->subWeeks($i),
            ]);
        }

        // ~14 months of weekly completion history ending at today's real figure
        $completion = Student::averageCompletion();

        for ($i = 60; $i >= 0; $i--) {
            MetricSnapshot::create([
                'metric'      => 'completion',
                'value'       => round(max(0, $completion - $i * 0.2), 1),
                'recorded_on' => today()->subWeeks($i),
            ]);
        }
    }
}