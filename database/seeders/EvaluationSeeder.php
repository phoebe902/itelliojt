<?php

namespace Database\Seeders;

use App\Enums\EvaluationStatus;
use App\Models\Evaluation;
use App\Models\Student;
use Illuminate\Database\Seeder;

class EvaluationSeeder extends Seeder
{
    public function run(): void
    {
        Evaluation::query()->delete();   // makes this seeder safe to re-run

        // The three students from the design: score, status, days since submitted
        $specials = [
            'BSCS-2025-0089' => [4.6, EvaluationStatus::Submitted, 1],
            'BSIT-2025-0142' => [3.1, EvaluationStatus::Flagged,   2],
            'BSIT-2025-0118' => [4.9, EvaluationStatus::Submitted, 3],
        ];

        foreach (Student::with('company')->get() as $student) {
            $row = [
                'student_id'     => $student->id,
                'term'           => 'midterm',
                'evaluator_name' => $student->company?->supervisor_name ?? 'Unassigned',
            ];

            if (isset($specials[$student->student_number])) {
                [$score, $status, $daysAgo] = $specials[$student->student_number];

                Evaluation::create($row + [
                    'status'       => $status,
                    'score'        => $score,
                    'submitted_on' => now()->subDays($daysAgo),
                    'due_on'       => now()->subDays($daysAgo),
                ]);
                continue;
            }

            $roll = random_int(1, 100);

            if ($roll <= 5) {                                   // overdue
                $row += ['status' => EvaluationStatus::Pending, 'due_on' => now()->subDays(random_int(1, 10))];
            } elseif ($roll <= 14) {                            // pending, due this week
                $row += ['status' => EvaluationStatus::Pending, 'due_on' => now()->addDays(random_int(1, 7))];
            } elseif ($roll <= 20) {                            // awaiting review
                $score = random_int(30, 50) / 10;
                $daysAgo = random_int(1, 5);
                $row += [
                    'status'       => $score < 3.5 ? EvaluationStatus::Flagged : EvaluationStatus::Submitted,
                    'score'        => $score,
                    'submitted_on' => now()->subDays($daysAgo),
                    'due_on'       => now()->subDays($daysAgo),
                ];
            } else {                                            // already reviewed
                $daysAgo = random_int(6, 40);
                $row += [
                    'status'       => EvaluationStatus::Reviewed,
                    'score'        => random_int(35, 50) / 10,
                    'submitted_on' => now()->subDays($daysAgo),
                    'due_on'       => now()->subDays($daysAgo),
                ];
            }

            Evaluation::create($row);
        }
    }
}