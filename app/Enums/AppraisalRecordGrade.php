<?php

namespace App\Enums;

enum AppraisalRecordGrade: string
{
    case POOR = 'poor';
    case BELOW_AVERAGE = 'below_average';
    case SATISFACTORY = 'satisfactory';
    case GOOD = 'good';
    case EXCELLENT = 'excellent';

    public function label(): string
    {
        return match ($this) {
            self::POOR => 'Poor',
            self::BELOW_AVERAGE => 'Below Average',
            self::SATISFACTORY => 'Satisfactory',
            self::GOOD => 'Good',
            self::EXCELLENT => 'Excellent',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::POOR => "Performance in most of the tasks delivered by the company exceeds the standard and the results are remarkable and excellent. Those who have a specific impact on the company's operating performance or exceptionally good deeds are enough to be exemplary employees.",
            self::BELOW_AVERAGE => "Performance in most of the tasks delivered by the company exceeds the standard and the results are remarkable and excellent. Those who have a specific impact on the company's operating performance or exceptionally good deeds are enough to be exemplary employees.",
            self::SATISFACTORY => "Performance can perform the tasks assigned by the company step by step and meet the standards.",
            self::GOOD => "	Performance can exceed the standard in some cases in the tasks delivered by the company, and the overall performance exceeds the general standard.",
            self::EXCELLENT => "Performance in most of the tasks delivered by the company exceeds the standard and the results are remarkable and excellent. Those who have a specific impact on the company's operating performance or exceptionally good deeds are enough to be exemplary employees",
        };
    }

    public function emoji(): string
    {
        return match ($this) {
            self::POOR => 'fa-solid fa-face-frown',
            self::BELOW_AVERAGE => 'fa-solid fa-face-meh',
            self::SATISFACTORY => 'fa-solid fa-face-smile',
            self::GOOD => 'fa-solid fa-face-smile-beam',
            self::EXCELLENT => 'fa-solid fa-face-grin-stars',
        };
    }

    public function score_range(): array
    {
        return match ($this) {
            self::POOR => [0, 59.99],
            self::BELOW_AVERAGE => [60, 69.99],
            self::SATISFACTORY => [70, 79.99],
            self::GOOD => [80, 89.99],
            self::EXCELLENT => [90, 100],
        };
    }
}
