<?php

namespace App\Contexts\Tweet\Domain\DTOs;

use App\Contexts\Tweet\Infrastructure\Http\Requests\CreateTweetRequest;

class TweetDTO
{
    private function __construct(
        private readonly string $userId,
        private readonly string $content
    ) {
    }

    public static function fromRequest(CreateTweetRequest $request): self
    {
        return new self(
            $request->user->id ?? $request->header('X-User-ID') ?? throw new \InvalidArgumentException('User ID not provided', 404),
            $request->get('content')
        );
    }

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function getContent(): string
    {
        return $this->content;
    }
}