<?php

namespace App\Contexts\Timeline\Domain\Models;

class Timeline
{
    private array $entries = [];
    private int $totalEntries = 0;

    public function __construct(
        private readonly string $userId,
        private readonly int $pageSize = 20
    ) {
    }

    public function addEntry(TimelineEntry $entry): void
    {
        array_unshift($this->entries, $entry);
        $this->totalEntries++;
    }

    public function getEntries(): array
    {
        return $this->entries;
    }

    public function getTotalEntries(): int
    {
        return $this->totalEntries;
    }
}