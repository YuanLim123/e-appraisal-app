<?php

namespace App\Enums;

enum AppraisalRecordGrade: string
{
    case POOR = 'Poor';
    case BELOW_AVERAGE = 'Below Average';
    case SATISFACTORY = 'Satisfactory';
    case GOOD = 'Good';
    case EXCELLENT = 'Excellent';

    public function label(): string
    {
        return match ($this) {
            self::POOR => "Performance in most of the tasks delivered by the company exceeds the standard and the results are remarkable and excellent. Those who have a specific impact on the company's operating performance or exceptionally good deeds are enough to be exemplary employees.",
            self::BELOW_AVERAGE => "Performance in most of the tasks delivered by the company exceeds the standard and the results are remarkable and excellent. Those who have a specific impact on the company's operating performance or exceptionally good deeds are enough to be exemplary employees.",
            self::SATISFACTORY => "Performance can perform the tasks assigned by the company step by step and meet the standards.",
            self::GOOD => "	Performance can exceed the standard in some cases in the tasks delivered by the company, and the overall performance exceeds the general standard.",
            self::EXCELLENT => "Performance in most of the tasks delivered by the company exceeds the standard and the results are remarkable and excellent. Those who have a specific impact on the company's operating performance or exceptionally good deeds are enough to be exemplary employees"
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::POOR => "😞",
            self::BELOW_AVERAGE => "😐",
            self::SATISFACTORY => "🙂",
            self::GOOD => "😀",
            self::EXCELLENT => "🤩"
        };
    }

    public function score_range(): array
    {
        return match ($this) {
            self::POOR => [0, 59],
            self::BELOW_AVERAGE => [60, 69],
            self::SATISFACTORY => [70, 79],
            self::GOOD => [80, 89],
            self::EXCELLENT => [90, 100]
        };
    }
}
