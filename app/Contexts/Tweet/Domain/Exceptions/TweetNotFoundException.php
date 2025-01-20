<?php

namespace App\Contexts\Tweet\Domain\Exceptions;

use App\Shared\Domain\Exceptions\DomainException;
use Symfony\Component\HttpFoundation\Response;

class TweetNotFoundException extends DomainException
{
    protected $code = Response::HTTP_NOT_FOUND;

    public function __construct(string $id)
    {
        parent::__construct("Tweet with id {$id} not found.");
    }
}