<?php

namespace App\Contexts\User\Domain\Exceptions;

use Symfony\Component\HttpFoundation\Response;

class CannotFollowYourselfException extends DomainException
{
    protected $code = Response::HTTP_BAD_REQUEST;

    public function __construct()
    {
        parent::__construct("Cannot follow yourself.");
    }
}
