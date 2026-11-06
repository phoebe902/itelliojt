<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Competency extends Model
{
    protected $fillable =[
    'name',
    'benchmark',
    'sort_order',
    ];

    public function students(): BelongsToMany{
        return $this->belongsToMany(Student::class)
        ->withPivot('score');
    }
}
