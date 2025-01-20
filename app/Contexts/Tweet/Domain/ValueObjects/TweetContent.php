<?php

namespace App\Contexts\Tweet\Domain\ValueObjects;

use App\Contexts\Tweet\Domain\Exceptions\EmptyTweetContentException;
use App\Contexts\Tweet\Domain\Exceptions\TweetContentTooLongException;

class TweetContent
{
    private string $content;
    private const MAX_LENGTH = 280;

    public function __construct(string $content)
    {
        $content = trim($content);
        $this->validate($content);
        $this->content = $content;
    }

    private function validate(string $content): void
    {
        if (empty($content)) {
            throw new EmptyTweetContentException();
        }

        if (mb_strlen($content) > self::MAX_LENGTH) {
            throw new TweetContentTooLongException(self::MAX_LENGTH);
        }
    }

    public function __toString(): string
    {
        return $this->content;
    }

    public function equals(TweetContent $other): bool
    {
        return $this->content === $other->content;
    }
}