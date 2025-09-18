<?php

namespace App\Exceptions;

use Exception;

class UserHasNoAppraisalCreated extends Exception
{
    protected $message = 'The user does not have an appraisal. Please create an appraisal first before creating an appraisal record.';
}
