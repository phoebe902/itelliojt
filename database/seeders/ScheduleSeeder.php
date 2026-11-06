<?php

namespace Database\Seeders;

use App\Enums\EventType;
use App\Models\ScheduleEvent;
use App\Models\Student;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class ScheduleSeeder extends Seeder
{
    public function run(): void
    {
        ScheduleEvent::query()->delete();   // safe to re-run

        $joshua = Student::where('student_number', 'BSIT-2025-0142')->first();

        // day offset from Monday, start time, minutes, title, type, location, student id
        $template = [
            [0, '09:00', 45, 'Coordinator briefing', EventType::Meeting,      'Admin Office',    null],
            [0, '13:00', 60, 'Student intervention', EventType::Intervention, 'Room 204',        $joshua?->id],
            [1, '10:00', 60, 'Vertex site visit',    EventType::Visit,        'On-site',         null],
            [3, '14:00', 60, 'Partner orientation',  EventType::Orientation,  'Online',          null],
            [4, '11:00', 60, 'Midterm deadline',     EventType::Deadline,     'All evaluations', null],
        ];

        // This week and next week
        foreach ([0, 1] as $week) {
            $monday = today()->startOfWeek(Carbon::MONDAY)->addWeeks($week);

            foreach ($template as [$day, $time, $minutes, $title, $type, $location, $studentId]) {
                $start = $monday->copy()->addDays($day)->setTimeFromTimeString($time);

                ScheduleEvent::create([
                    'title'      => $title,
                    'type'       => $type,
                    'student_id' => $studentId,
                    'location'   => $location,
                    'starts_at'  => $start,
                    'ends_at'    => $start->copy()->addMinutes($minutes),
                ]);
            }
        }
    }
}