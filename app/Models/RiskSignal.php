<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class RiskSignal extends Model
{
    protected $fillable = ['label'];

    public function students(): BelongsToMany{
        return $this->belongsToMany(Student::class);
    }
}
