<?php

namespace App\Contexts\User\Domain\Exceptions;

use Exception;
use Symfony\Component\HttpFoundation\Response;

abstract class DomainException extends Exception
{
    protected $code = Response::HTTP_UNPROCESSABLE_ENTITY;
}
