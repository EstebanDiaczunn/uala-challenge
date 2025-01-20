<?php

namespace App\Contexts\Tweet\Domain\Events;

use App\Contexts\Tweet\Domain\Models\Tweet;

class TweetCreatedEvent implements DomainEvent
{
    private string $occurredOn;

    public function __construct(
        private Tweet $tweet
    ) {
        $this->occurredOn = (new \DateTime())->format('Y-m-d\TH:i:s.uP');
    }

    public function getTweet(): Tweet
    {
        return $this->tweet;
    }

    public function getOccurredOn(): string
    {
        return $this->occurredOn;
    }

    public function toArray(): array
    {
        return [
            'tweet_id' => $this->tweet->getId(),
            'user_id' => $this->tweet->getUserId(),
            'content' => $this->tweet->getContent(),
            'created_at' => $this->tweet->getCreatedAt()->format('Y-m-d H:i:s')
        ];
    }
}