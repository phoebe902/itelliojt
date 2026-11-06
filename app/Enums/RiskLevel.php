<?php
namespace App\Enums;

enum RiskLevel:string
{
    case OnTrack= 'on_track';
    case Medium = 'medium';
    case High = 'high';

    public function label(): string
    {
        return match($this){
            self::OnTrack=>'On Track',
            self::Medium => 'Medium',
            self::High => "High Risk",
        };
    }
    public function dot():string
    {
        return match($this){
            self::OnTrack => 'bg-emerald-500',
            self::Medium => 'bg-amber-400',
            self::High => 'bg-rose-500',
        };
    }
    public function badge():string{
        return match($this){
            self::OnTrack => 'bg-emerald-50 text-emerald-700',
            self::Medium => 'bg-amber-50 text-amber-700',
            self::High => 'bg-rose-50 text-rose-700',
        };
    }
    public function avatar():string{
        return match($this){
            self::OnTrack => 'bg-emerald-50 text-emerald-700',
            self::Medium => 'bg-amber-50 text-amber-700',
            self::High => 'bg-rose-50 text-rose-700',
        };
    }
}