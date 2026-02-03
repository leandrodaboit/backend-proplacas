<?php

namespace App\Exceptions;

use Exception;

class AuthenticationException extends Exception
{
    protected $code = 401;
}
