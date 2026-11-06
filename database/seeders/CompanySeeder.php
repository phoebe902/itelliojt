<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    public function run(): void
    {
        $named = [
            ['TechCore Solutions',  'Software Development', 14, 4.8, 'Ramon Diaz'],
            ['Northpeak Digital',   'Digital Marketing',    10, 4.6, 'Jenna Cruz'],
            ['Cloudline Labs',      'Cloud Infrastructure', 16, 4.9, 'Albert Tan'],
            ['Vertex Systems',      'Cybersecurity',        12, 4.5, 'Vera Santos'],
            ['Nexora Technologies', 'Telecommunications',   10, 4.4, 'Nico Reyes'],
            ['BrightIdeas Inc',     'Data Analytics',       12, 4.7, 'Bea Ignacio'],
        ];

        foreach ($named as [$name, $industry, $capacity, $rating, $supervisor]) {
            Company::updateOrCreate(['name' => $name], [
                'industry'        => $industry,
                'capacity'        => $capacity,
                'rating'          => $rating,
                'supervisor_name' => $supervisor,
            ]);
        }

        Company::factory(26)->create();   // 6 + 26 = 32 partners
    }
}