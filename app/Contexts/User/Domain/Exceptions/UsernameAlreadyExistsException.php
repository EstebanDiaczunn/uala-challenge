<?php

namespace App\Contexts\User\Domain\Exceptions;

class UsernameAlreadyExistsException extends DomainException
{
    public function __construct(string $username)
    {
        parent::__construct("Username '{$username}' already exists.");
    }
}
