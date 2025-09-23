<?php

namespace App\Exceptions;

use Exception;

class AppraisalHasNoApproverException extends Exception
{
    protected $message = 'The appraisal has no approver assigned. 
        please add an approver to the appraisal before submitting.';
}
