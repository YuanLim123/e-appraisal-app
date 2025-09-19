<?php

namespace App\Exceptions;

use Exception;

class InvalidRatingSumException extends Exception
{
    protected $message = 'Section 1 value is invalid. The sum of ratings must not exceed 100%.';
}
