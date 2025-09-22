<?php

namespace App\Exceptions;

use Exception;

class InvalidWeightAgeException extends Exception
{
    protected $message = 'The sum of weightage must be 100.';
}
