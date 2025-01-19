<?php

namespace App\Contexts\User\Domain\Exceptions;

class UserNotFoundException extends DomainException
{
    public function __construct(string $id)
    {
        parent::__construct("User id '{$id}' not found.");
    }
}
