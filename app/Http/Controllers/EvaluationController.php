<?php

namespace App\Http\Controllers;

use App\Models\Evaluation;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\Setting;

class EvaluationController extends Controller
{
    public function index(Request $request)
    {
        $term = (string) $request->query('term', Setting::get('current_term', 'midterm'));
        $showAll = $request->boolean('all');

        $base = Evaluation::term($term);

        $total     = (clone $base)->count();
        $submitted = (clone $base)->submitted()->count();

        $stats = [
            'awaiting'    => (clone $base)->awaitingReview()->count(),
            'dueSoon'     => (clone $base)->pending()->whereBetween('due_on', [today(), today()->addDays(7)])->count(),
            'submitted'   => $submitted,
            'pending'     => (clone $base)->pending()->where('due_on', '>=', today())->count(),
            'overdue'     => (clone $base)->pending()->where('due_on', '<', today())->count(),
            'total'       => $total,
            'percent'     => $total > 0 ? (int) round($submitted / $total * 100) : 0,
        ];

        $nextDeadline = (clone $base)->pending()->where('due_on', '>=', today())->min('due_on');

        $queue = Evaluation::term($term)
            ->submitted()
            ->with('student.company')
            ->orderByDesc('submitted_on')
            ->orderByDesc('id');

        return view('evaluations.index', [
            'term'         => $term,
            'showAll'      => $showAll,
            'stats'        => $stats,
            'nextDeadline' => $nextDeadline ? Carbon::parse($nextDeadline) : null,
            'evaluations'  => $showAll ? $queue->paginate(10)->withQueryString() : $queue->take(5)->get(),
        ]);
    }

    public function remind(Request $request)
    {
        $count = Evaluation::term((string) $request->input('term', 'midterm'))->pending()->count();

        // TODO: send a reminder notification/email to each evaluator with a pending evaluation

        return back()->with('status', "{$count} pending evaluations are ready for reminders. Email sending isn't connected yet.");
    }
}