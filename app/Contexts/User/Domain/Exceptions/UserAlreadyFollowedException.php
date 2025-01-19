<?php

namespace App\Contexts\User\Domain\Exceptions;

class UserAlreadyFollowedException extends DomainException
{
    public function __construct($id)
    {
        parent::__construct("You are already following user {$id}.");
    }
}
