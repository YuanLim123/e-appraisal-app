<?php

namespace App\Exceptions;

use Exception;

class InvalidAppraisalSeason extends Exception
{
    protected $message = 'The appraisal season has not been started yet. Please try again later.';
}
