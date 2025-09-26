<?php

namespace App\Exceptions;

use Exception;

class RecordAlreadySubmitException extends Exception
{
    protected $message = 'Appraisal record has already been submitted.';
}
