<?php

namespace App\Enums;

enum EventType: string
{
    case Meeting      = 'meeting';
    case Visit        = 'visit';
    case Intervention = 'intervention';
    case Orientation  = 'orientation';
    case Deadline     = 'deadline';

    public function label(): string
    {
        return match ($this) {
            self::Meeting      => 'Meeting',
            self::Visit        => 'Site visit',
            self::Intervention => 'Intervention',
            self::Orientation  => 'Orientation',
            self::Deadline     => 'Deadline',
        };
    }

    /** Calendar block colours */
    public function block(): string
    {
        return match ($this) {
            self::Meeting      => 'bg-blue-100 border-blue-500 text-blue-900',
            self::Visit        => 'bg-violet-100 border-violet-500 text-violet-900',
            self::Intervention => 'bg-rose-100 border-rose-500 text-rose-900',
            self::Orientation  => 'bg-amber-100 border-amber-500 text-amber-900',
            self::Deadline     => 'bg-emerald-100 border-emerald-500 text-emerald-900',
        };
    }

    /** Coloured bar in the "Today" list */
    public function bar(): string
    {
        return match ($this) {
            self::Meeting      => 'bg-blue-500',
            self::Visit        => 'bg-violet-500',
            self::Intervention => 'bg-rose-500',
            self::Orientation  => 'bg-amber-500',
            self::Deadline     => 'bg-emerald-500',
        };
    }
}