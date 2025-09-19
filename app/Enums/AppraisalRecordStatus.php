<?php

namespace App\Enums;

enum AppraisalRecordStatus: string
{
    case CREATED = 'created';
    case SUBMITTED = 'submitted';
    case COMPLETED = 'completed';
    case REJECTED = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::CREATED => 'Created',
            self::SUBMITTED => 'Submitted',
            self::COMPLETED => 'Completed',
            self::REJECTED => 'Rejected',
        };
    }
}
