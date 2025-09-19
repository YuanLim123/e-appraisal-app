<?php

namespace App\Exceptions;

use Exception;

class RecordAlreadyExistsInSeasonException extends Exception
{
    protected $message = 'The user already has an appraisal record in the selected season.';
}
