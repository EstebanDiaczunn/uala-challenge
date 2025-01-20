<?php

namespace App\Contexts\User\Domain\Exceptions;

use App\Shared\Domain\Exceptions\DomainException;
use Symfony\Component\HttpFoundation\Response;

class CannotUnfollowYourselfException extends DomainException
{
    protected $code = Response::HTTP_BAD_REQUEST;

    public function __construct()
    {
        parent::__construct("Cannot unfollow yourself.");
    }
}
