<?php

namespace App\Http\Controllers;

use App\Enums\EventType;
use App\Models\Evaluation;
use App\Models\ScheduleEvent;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;

class ScheduleController extends Controller
{
    private const START_HOUR = 8;    // first row of the calendar
    private const END_HOUR   = 18;   // calendar ends at 6 PM
    private const HOUR_PX    = 60;   // must match the 60px-per-hour grid in the view

    public function index(Request $request)
    {
        try {
            $anchor = $request->filled('week') ? Carbon::parse($request->query('week')) : today();
        } catch (\Throwable) {
            $anchor = today();
        }

        $monday = $anchor->copy()->startOfWeek(Carbon::MONDAY);
        $friday = $monday->copy()->addDays(4);

        $events = ScheduleEvent::with('student')
            ->whereBetween('starts_at', [$monday, $friday->copy()->endOfDay()])
            ->orderBy('starts_at')
            ->get();

        $days = collect(range(0, 4))->map(function ($i) use ($monday, $events) {
            $date = $monday->copy()->addDays($i);

            return [
                'date'    => $date,
                'isToday' => $date->isToday(),
                'items'   => $this->layout($events->filter(fn ($e) => $e->starts_at->isSameDay($date))),
            ];
        });

        $label = $monday->isSameMonth($friday)
            ? $monday->format('F j') . '–' . $friday->format('j, Y')
            : $monday->format('M j') . ' – ' . $friday->format('M j, Y');

        // Current-time line (only drawn inside today's column)
        $nowMinutes = $this->minutesFromStart(now());
        $nowTop     = ($nowMinutes >= 0 && $nowMinutes <= $this->totalMinutes()) ? $nowMinutes : null;

        return view('schedule.index', [
            'label'       => $label,
            'prevWeek'    => $monday->copy()->subWeek()->toDateString(),
            'nextWeek'    => $monday->copy()->addWeek()->toDateString(),
            'days'        => $days,
            'hours'       => range(self::START_HOUR, self::END_HOUR - 1),
            'gridHeight'  => $this->totalMinutes(),
            'nowTop'      => $nowTop,
            'todayEvents' => ScheduleEvent::with('student')
                ->whereBetween('starts_at', [today(), today()->endOfDay()])
                ->orderBy('starts_at')
                ->get(),
            'deadline'    => $this->nextEvaluationDeadline(),
            'types'       => EventType::cases(),
            'students'    => Student::orderBy('last_name')->orderBy('first_name')->get(['id', 'first_name', 'last_name']),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'      => ['required', 'string', 'max:120'],
            'type'       => ['required', Rule::enum(EventType::class)],
            'date'       => ['required', 'date'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time'   => ['nullable', 'date_format:H:i', 'after:start_time'],
            'location'   => ['nullable', 'string', 'max:120'],
            'student_id' => ['nullable', 'exists:students,id'],
        ]);

        $starts = Carbon::parse("{$data['date']} {$data['start_time']}");

        ScheduleEvent::create([
            'title'      => $data['title'],
            'type'       => $data['type'],
            'student_id' => $data['student_id'] ?? null,
            'location'   => $data['location'] ?? null,
            'starts_at'  => $starts,
            'ends_at'    => ! empty($data['end_time']) ? Carbon::parse("{$data['date']} {$data['end_time']}") : null,
        ]);

        return redirect()
            ->route('schedule.index', ['week' => $starts->copy()->startOfWeek(Carbon::MONDAY)->toDateString()])
            ->with('status', 'Event added to the schedule.');
    }

    /** "Important deadline" card: next pending midterm evaluation due date */
    private function nextEvaluationDeadline(): ?array
    {
        $nextDue = Evaluation::term('midterm')->pending()->where('due_on', '>=', today())->min('due_on');

        if (! $nextDue) {
            return null;
        }

        $days = (int) today()->diffInDays(Carbon::parse($nextDue), false);

        return [
            'title'     => 'Midterm evaluations',
            'remaining' => Evaluation::term('midterm')->pending()->count(),
            'label'     => match (true) {
                $days <= 0  => 'Due today',
                $days === 1 => 'Due tomorrow',
                default     => "Due in {$days} days",
            },
        ];
    }

    /**
     * Turn a day's events into positioned blocks. Overlapping events
     * are split into side-by-side lanes.
     */
    private function layout(Collection $events): array
    {
        $events = $events->sortBy('starts_at')->values();

        // 1. Group overlapping events into clusters
        $clusters   = [];
        $current    = [];
        $clusterEnd = null;

        foreach ($events as $event) {
            $end = $this->endOf($event);

            if ($clusterEnd !== null && $event->starts_at >= $clusterEnd) {
                $clusters[] = $current;
                $current    = [];
                $clusterEnd = null;
            }

            $current[]  = $event;
            $clusterEnd = ($clusterEnd === null || $end > $clusterEnd) ? $end : $clusterEnd;
        }

        if ($current) {
            $clusters[] = $current;
        }

        // 2. Give each event in a cluster its own lane, then position it
        $items = [];
        $total = $this->totalMinutes();

        foreach ($clusters as $cluster) {
            $laneEnds = [];
            $placed   = [];

            foreach ($cluster as $event) {
                $lane = null;

                foreach ($laneEnds as $i => $laneEnd) {
                    if ($event->starts_at >= $laneEnd) {
                        $lane = $i;
                        break;
                    }
                }

                $lane ??= count($laneEnds);
                $laneEnds[$lane] = $this->endOf($event);
                $placed[] = [$event, $lane];
            }

            $lanes = count($laneEnds);

            foreach ($placed as [$event, $lane]) {
                $start = max(0, $this->minutesFromStart($event->starts_at));
                $end   = min($total, $this->minutesFromStart($this->endOf($event)));

                if ($end <= $start) {
                    continue;   // outside the visible hours
                }

                $items[] = [
                    'event'  => $event,
                    'top'    => $start * self::HOUR_PX / 60,
                    'height' => max(30, ($end - $start) * self::HOUR_PX / 60),
                    'left'   => $lane / $lanes * 100,
                    'width'  => 100 / $lanes,
                ];
            }
        }

        return $items;
    }

    private function endOf(ScheduleEvent $event): Carbon
    {
        return $event->ends_at ?? $event->starts_at->copy()->addHour();
    }

    private function minutesFromStart(Carbon $time): int
    {
        return ($time->hour - self::START_HOUR) * 60 + $time->minute;
    }

    private function totalMinutes(): int
    {
        return (self::END_HOUR - self::START_HOUR) * 60;
    }
}