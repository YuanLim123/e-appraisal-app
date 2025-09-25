<?php

namespace App\Exceptions;

use Exception;

class AgreementRequiredException extends Exception
{
    protected $message = 'Both employee and supervisor must acknowledge before submission.';
}
