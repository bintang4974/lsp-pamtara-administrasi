<?php

namespace App\Enums;

enum AssessmentResult: string
{
    case Competent = 'competent';
    case NotCompetent = 'not_competent';

    public function label(): string
    {
        return match ($this) {
            self::Competent => 'Kompeten',
            self::NotCompetent => 'Tidak Kompeten',
        };
    }
}
