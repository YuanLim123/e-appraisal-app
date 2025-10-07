<?php

namespace App\Exceptions;

use Exception;

class ApproverNotFoundException extends Exception
{
    protected $message = 'This appraisal record has no pending approver';
}
