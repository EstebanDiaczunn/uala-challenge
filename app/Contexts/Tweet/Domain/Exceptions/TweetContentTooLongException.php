<?php

namespace App\Contexts\Tweet\Domain\Exceptions;

use App\Shared\Domain\Exceptions\DomainException;
use Symfony\Component\HttpFoundation\Response;

class TweetContentTooLongException extends DomainException
{
    public function __construct(int $maxLength)
    {
        parent::__construct("Tweet content cannot exceed {$maxLength} characters.", Response::HTTP_BAD_REQUEST);
    }
}