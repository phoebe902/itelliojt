<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class MetricSnapshot extends Model
{
    protected $fillable = ['metric', 'value', 'recorded_on'];

    protected function casts(): array
    {
        return [
            'value'       => 'float',
            'recorded_on' => 'date',
        ];
    }

    public function scopeMetric(Builder $query, string $metric): Builder
    {
        return $query->where('metric', $metric);
    }
}