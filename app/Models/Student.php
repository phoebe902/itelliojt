<?php

namespace App\Models;

use App\Enums\RiskLevel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_number', 'first_name', 'last_name', 'company_id',
        'required_hours', 'rendered_hours', 'risk_level',
    ];

    protected function casts(): array
    {
        return ['risk_level' => RiskLevel::class];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
/** Average OJT completion across all students, 0-100 */
public static function averageCompletion(): float
{
    return (float) static::query()
        ->selectRaw('AVG(rendered_hours / NULLIF(required_hours, 0)) * 100 as pct')
        ->value('pct');
}
    protected function fullName(): Attribute
    {
        return Attribute::get(fn () => "{$this->first_name} {$this->last_name}");
    }

    protected function initials(): Attribute
    {
        return Attribute::get(fn () => strtoupper(
            mb_substr($this->first_name, 0, 1) . mb_substr($this->last_name, 0, 1)
        ));
    }

    protected function progress(): Attribute
    {
        return Attribute::get(fn () => $this->required_hours > 0
            ? min(100, (int) round($this->rendered_hours / $this->required_hours * 100))
            : 0);
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        return $query->when($term, fn ($q) => $q->where(function ($q) use ($term) {
            $q->where('first_name', 'like', "%{$term}%")
              ->orWhere('last_name', 'like', "%{$term}%")
              ->orWhere('student_number', 'like', "%{$term}%")
              ->orWhereHas('company', fn ($c) => $c->where('name', 'like', "%{$term}%"));
        }));
    }
}