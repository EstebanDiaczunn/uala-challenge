<?php

namespace App\Contexts\Tweet\Domain\Models;

use App\Contexts\Tweet\Domain\Events\TweetCreatedEvent;
use App\Contexts\Tweet\Domain\ValueObjects\TweetContent;
use MongoDB\BSON\ObjectId;
use DateTime;

class Tweet
{
    private ObjectId $id;
    private string $userId;
    private TweetContent $content;
    private DateTime $createdAt;
    private array $events = [];

    public function __construct(
        string $userId,
        TweetContent $content,
        ?ObjectId $id = null,
        bool $recordEvents = true
    ) {
        $this->id = $id ?? new ObjectId();
        $this->userId = $userId;
        $this->content = $content;
        $this->createdAt = new DateTime();

        // Solo registramos el evento si se indica
        if ($recordEvents) {
            $this->recordEvent(new TweetCreatedEvent($this));
        }
    }

    // Método estático para reconstrucción
    public static function reconstruct(
        string $userId,
        TweetContent $content,
        ObjectId $id
    ): self {
        // Usamos el constructor con recordEvents = false
        return new self($userId, $content, $id, false);
    }

    public function getId(): string
    {
        return (string) $this->id;
    }

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function getContent(): string
    {
        return (string) $this->content;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    private function recordEvent($event): void
    {
        $this->events[] = $event;
    }

    public function releaseEvents(): array
    {
        $events = $this->events;
        $this->events = [];
        return $events;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'user_id' => $this->getUserId(),
            'content' => $this->getContent(),
            'created_at' => $this->getCreatedAt()->format('Y-m-d H:i:s'),
        ];
    }
}