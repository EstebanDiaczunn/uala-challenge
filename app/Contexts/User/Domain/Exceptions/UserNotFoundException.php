<?php

namespace App\Contexts\User\Domain\Exceptions;

use App\Shared\Domain\Exceptions\DomainException;

class UserNotFoundException extends DomainException
{
    public function __construct(string $id)
    {
        parent::__construct("User id '{$id}' not found.");
    }
}
