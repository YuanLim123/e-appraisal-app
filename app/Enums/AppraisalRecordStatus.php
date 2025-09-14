<?php

namespace App\Enums;

enum AppraisalRecordStatus: string
{
    case CREATED = 'created';
    case SUBMITTED = 'submitted';
    case COMPLETED = 'completed';
    case REJECTED = 'rejected';
}
