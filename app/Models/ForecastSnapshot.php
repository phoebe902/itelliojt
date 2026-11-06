<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ForecastSnapshot extends Model
{
    protected $fillable= [
        'success_rate',
        'model_confidence',
        'calculated_at',
    ];

    protected function casts(): array
    {
        return[
            'success_rate'=>'float',
            'model_confidence'=>'float',
            'calculated_at'=>'datetime',
        ];
    }
}
