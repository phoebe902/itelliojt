<?php

namespace Database\Seeders;

use App\Models\Program;
use App\Models\Student;
use Illuminate\Database\Seeder;

class ProgramSeeder extends Seeder
{
    public function run(): void
    {
        $programs = [
            ['BSIT', 'BS Information Technology',  'College of Computer Studies'],
            ['BSCS', 'BS Computer Science',        'College of Computer Studies'],
            ['BSIS', 'BS Information Systems',     'College of Computer Studies'],
        ];

        foreach ($programs as [$code, $name, $department]) {
            $program = Program::updateOrCreate(
                ['code' => $code],
                ['name' => $name, 'department' => $department, 'required_hours' => 486, 'is_active' => true],
            );

            // Link existing students whose student number starts with the program code
            Student::where('student_number', 'like', $code . '-%')->update(['program_id' => $program->id]);
        }
    }
}