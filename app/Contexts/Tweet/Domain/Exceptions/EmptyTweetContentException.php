<?php

namespace App\Contexts\Tweet\Domain\Exceptions;

use App\Shared\Domain\Exceptions\DomainException;
use Symfony\Component\HttpFoundation\Response;

class EmptyTweetContentException extends DomainException
{
    protected $code = Response::HTTP_BAD_REQUEST;

    public function __construct()
    {
        parent::__construct("Tweet content cannot be empty.");
    }
}