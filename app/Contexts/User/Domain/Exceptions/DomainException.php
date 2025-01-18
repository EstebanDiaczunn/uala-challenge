<?php

namespace App\Contexts\User\Domain\Exceptions;

use Exception;

abstract class DomainException extends Exception
{
    protected $code = 422;
}
