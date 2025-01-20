<?php

namespace App\Contexts\User\Domain\Exceptions;

use App\Shared\Domain\Exceptions\DomainException;

class UserAlreadyFollowedException extends DomainException
{
    public function __construct($id)
    {
        parent::__construct("You are already following user {$id}.");
    }
}
