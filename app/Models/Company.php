<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Company extends Model
{
    /** @use HasFactory<\Database\Factories\CompanyFactory> */
    use HasFactory;
    protected $fillable= ['name', 'industry', 'capacity', 'rating', 'supervisor_name'];
     public function casts(): array
     {
        return [
            'rating'=>'decimal:1',
            'is_active'=> 'boolean'
        ];
     }
    public function students():HasMany
    {
        return $this->hasMany(Student::class);
    }
    protected function initials(): Attribute
    {
        return Attribute::get(fn () => self::makeInitials($this->name));
    }
    public function supervisorInitials(): Attribute
    {
        return Attribute::get(fn() => self::makeInitials($this->supervisor_name??''));
    }

    protected function openSlots():Attribute{
        return Attribute::get(fn ()=> max(
            0,
            $this->capacity - ($this->students_count ?? $this->students()->count())
        ));
    }
    private static function makeInitials(string $text): string
    {
        return Str::of($text)->headline()->explode(' ')->filter()->take(2)
            ->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))->implode('');
    }
}
