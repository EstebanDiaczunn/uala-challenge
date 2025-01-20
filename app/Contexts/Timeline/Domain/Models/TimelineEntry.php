<?php

namespace App\Contexts\Timeline\Domain\Models;

class TimelineEntry
{
    public function __construct(
        private readonly string $tweetId,
        private readonly string $userId,
        private readonly string $content,
        private readonly \DateTime $createdAt,
        private readonly array $metadata = []
    ) {
    }

    // Getters...
    public function getTweetId(): string
    {
        return $this->tweetId;
    }

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }
}