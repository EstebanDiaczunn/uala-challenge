<?php

namespace App\Contexts\Tweet\Domain\Events;

interface DomainEvent
{
    public function getOccurredOn(): string;
    public function toArray(): array;
}