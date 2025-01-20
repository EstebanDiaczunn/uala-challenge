<?php

namespace App\Contexts\User\Domain\Exceptions;

use App\Shared\Domain\Exceptions\DomainException;

class UsernameAlreadyExistsException extends DomainException
{
    public function __construct(string $username)
    {
        parent::__construct("Username '{$username}' already exists.");
    }
}
