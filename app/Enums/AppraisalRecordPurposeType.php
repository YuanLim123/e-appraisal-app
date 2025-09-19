<?php

namespace App\Enums;

enum AppraisalRecordPurposeType: string
{
    case CONFIRMATION_OR_PROMOTION = 'confirmation_or_promotion';
    case ANNUAL_REVIEW = 'annual_review';
    case SPECIAL_REVIEW = 'special_review';

    public function label(): string
    {
        return match ($this) {
            self::CONFIRMATION_OR_PROMOTION => 'Confirmation or Promotion',
            self::ANNUAL_REVIEW => 'Annual Review',
            self::SPECIAL_REVIEW => 'Special Review',
        };
    }
}
