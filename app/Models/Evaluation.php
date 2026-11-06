<?php

namespace App\Models;

use App\Enums\EvaluationStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Evaluation extends Model
{
    protected $fillable = [
        'student_id', 
        'term', 
        'evaluator_name', 
        'status', 'score', 
        'due_on',
        'submitted_on',
    ];

    protected function casts(): array
    {
        return [
            'status' => EvaluationStatus::class,
            'score' => 'decimal:1',
            'due_on' => 'date',
            'submitted_on' => 'date',
        ];
    }

    public function student(): BelongsTo{
        return $this -> belongsTo(Student::class);

    }

    public function scopeTerm(Builder $query, string $term):Builder{
        return $query-> where('term', $term);
    }
    public function scopeAwaitingReview(Builder $query):Builder{
        return $query->whereIn('status', EvaluationStatus::awaitingReviewValues());
    }

    public function scopeSubmitted(Builder $query): Builder{
        return $query ->whereIn('status', EvaluationStatus::submittedValues());
    }

    public function scopePending(Builder $query):Builder{
        return $query->where('status', EvaluationStatus::Pending->value);
    }
}
