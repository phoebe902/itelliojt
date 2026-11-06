<?php

namespace App\Console\Commands;

use App\Models\MetricSnapshot;
use App\Models\Student;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RecordSnapshots extends Command
{
    protected $signature = 'ojt:snapshot';

    protected $description = 'Record the performance and completion numbers used by the dashboard trends';

    public function handle(): int
    {
        $values = [
            'completion'  => Student::averageCompletion(),
            'performance' => DB::table('competency_student')->avg('score'),   // average competency score
        ];

        foreach ($values as $metric => $value) {
            if ($value === null) {
                continue;
            }

            MetricSnapshot::updateOrCreate(
                ['metric' => $metric, 'recorded_on' => today()->toDateString()],
                ['value' => round($value, 1)],
            );
        }

        $this->info('Snapshots recorded.');

        return self::SUCCESS;
    }
}