<?php
namespace App\Enums;

enum EvaluationStatus:string{
    case Pending = 'pending';
    case Submitted = 'submitted';
    case Flagged = 'flagged';
    case Reviewed = 'reviewed';

    public function label():string{
        return match($this){

        self::Pending => 'Pending',
        self::Submitted => 'Submitted',
        self::Flagged => 'Flagged',
        self::Reviewed => 'Reviewed',

        };

    }
    public function badge(): string
    {
        return match ($this) {
            self::Pending   => 'bg-amber-50 text-amber-700',
            self::Submitted => 'bg-blue-50 text-blue-700',
            self::Flagged   => 'bg-rose-50 text-rose-700',
            self::Reviewed  => 'bg-emerald-50 text-emerald-700',
        };

    }
    public function avatar(): string
    {
        return match ($this) {
            self::Flagged => 'bg-rose-50 text-rose-600',
            default       => 'bg-blue-50 text-blue-600',
        };
    }

      /** @return string[] statuses that are waiting for staff review */
    public static function awaitingReviewValues(): array
    {
        return [self::Submitted->value, self::Flagged->value];
    }

    /** @return string[] statuses where the employer has already submitted */
    public static function submittedValues(): array
    {
        return [self::Submitted->value, self::Flagged->value, self::Reviewed->value];
    }



}

?>
