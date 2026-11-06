<?php

namespace App\Models;

use App\Enums\EventType;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScheduleEvent extends Model
{
    protected $fillable = ['title', 'type', 'student_id', 'location', 'starts_at', 'ends_at'];

    protected function casts(): array
    {
        return [
            'type'      => EventType::class,
            'starts_at' => 'datetime',
            'ends_at'   => 'datetime',
        ];
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    // "45 min", "1 hr", "1 hr 30 min" (null when there is no end time)
    protected function durationLabel(): Attribute
    {
        return Attribute::get(function () {
            if (! $this->ends_at) {
                return null;
            }

            $minutes = (int) round($this->starts_at->diffInMinutes($this->ends_at, true));
            $hours   = intdiv($minutes, 60);
            $rest    = $minutes % 60;

            return match (true) {
                $hours === 0 => "{$rest} min",
                $rest === 0  => "{$hours} hr",
                default      => "{$hours} hr {$rest} min",
            };
        });
    }

    // "Joshua Mendoza · Room 204 · 1 hr"
    protected function subtitle(): Attribute
    {
        return Attribute::get(fn () => collect([
            $this->student?->full_name,
            $this->location,
            $this->duration_label,
        ])->filter()->join(' · '));
    }
}