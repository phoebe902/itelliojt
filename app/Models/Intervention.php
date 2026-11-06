<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Intervention extends Model
{
    protected $fillable = ['student_id', 'type', 'improved', 'intervened_on'];

    protected function casts(): array {

    return[
        'improved'=> 'boolean',
        'intervened_on' => 'date',
    ];
    }

    protected function student(): BelongsTo{
        return $this->belongsTo(Student::class);
    }
}
