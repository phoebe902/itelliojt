<?php

return [
    'General' => [
        'program_name'  => ['label' => 'OJT program name', 'type' => 'text',  'default' => 'UNM OJT Program'],
        'institution'   => ['label' => 'Institution',      'type' => 'text',  'default' => 'University of Northwestern Mindanao'],
        'support_email' => ['label' => 'Support email',    'type' => 'email', 'default' => '', 'help' => 'Shown to users who need help.'],
    ],

    'OJT defaults' => [
        'default_hours' => ['label' => 'Default required hours', 'type' => 'number', 'default' => 486, 'min' => 1, 'max' => 2000,
                            'help' => 'Pre-filled when you add a program.'],
        'current_term'  => ['label' => 'Current evaluation term', 'type' => 'select', 'default' => 'midterm',
                            'options' => ['midterm' => 'Midterm', 'final' => 'Final'],
                            'help' => 'The term the Evaluations page shows by default.'],
        'reminder_days' => ['label' => 'Reminder lead time (days)', 'type' => 'number', 'default' => 3, 'min' => 0, 'max' => 30,
                            'help' => 'How many days before a deadline evaluators are reminded.'],
    ],

    'Registration' => [
        'registration_open' => ['label' => 'Allow public sign-up', 'type' => 'toggle', 'default' => true,
                                'help' => 'When off, the sign-up page is closed to new users.'],
        'approve_students'  => ['label' => 'Require approval for student accounts', 'type' => 'toggle', 'default' => false,
                                'help' => 'Other user types always need administrator approval.'],
    ],
];