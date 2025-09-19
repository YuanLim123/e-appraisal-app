<?php

namespace App\Enums;

enum AppraisalRecordType: string
{
    case SUPERVISION = 'supervision';
    case NORMAL = 'normal';

    public function label(): string
    {
        return match ($this) {
            self::SUPERVISION => 'Supervision',
            self::NORMAL => 'Normal',
        };
    }
}