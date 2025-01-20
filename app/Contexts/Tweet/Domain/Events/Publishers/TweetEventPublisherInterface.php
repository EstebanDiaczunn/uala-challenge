<?php

namespace App\Contexts\Tweet\Domain\Events\Publishers;

interface TweetEventPublisherInterface
{
    public function publish(array $events): void;
}