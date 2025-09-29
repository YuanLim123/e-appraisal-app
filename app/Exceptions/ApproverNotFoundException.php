<?php

namespace App\Exceptions;

use Exception;

class ApproverNotFoundException extends Exception
{
    protected $message = 'Approver not found on this appraisal record';
}
