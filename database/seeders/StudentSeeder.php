<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Student;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
{
    $companies = Company::orderBy('id')->get();

    // The three rows from the design
    Student::create(['student_number' => 'BSIT-2025-0142', 'first_name' => 'Joshua',  'last_name' => 'Mendoza',
        'company_id' => $companies[0]->id, 'rendered_hours' => 312, 'risk_level' => 'high']);
    Student::create(['student_number' => 'BSCS-2025-0089', 'first_name' => 'Angela',  'last_name' => 'Lim',
        'company_id' => $companies[1]->id, 'rendered_hours' => 379, 'risk_level' => 'medium']);
    Student::create(['student_number' => 'BSIT-2025-0118', 'first_name' => 'Mikaela', 'last_name' => 'Santos',
        'company_id' => $companies[2]->id, 'rendered_hours' => 418, 'risk_level' => 'on_track']);

    Student::factory(120)->create();
}
}
