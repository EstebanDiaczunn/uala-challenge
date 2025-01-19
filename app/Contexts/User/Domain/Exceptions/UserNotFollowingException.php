<?php

namespace App\Contexts\User\Domain\Exceptions;

use Symfony\Component\HttpFoundation\Response;

class UserNotFollowingException extends DomainException
{
    protected $code = Response::HTTP_BAD_REQUEST;

    public function __construct(string $id)
    {
        parent::__construct("Already not following user {$id}.");
    }
}
