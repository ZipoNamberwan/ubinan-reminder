<?php

namespace App\Exceptions;

use Exception;

class NoScheduleException extends Exception
{
    protected $message = 'This is a custom exception.';
}