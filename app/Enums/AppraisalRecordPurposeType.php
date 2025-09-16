<?php

namespace App\Enums;

enum AppraisalRecordPurposeType: string
{
    case CONFIRMATION_OR_PROMOTION = 'confirmation_or_promotion';
    case ANNUAL_REVIEW = 'annual_review';
    case SPECIAL_REVIEW = 'special_review';
}