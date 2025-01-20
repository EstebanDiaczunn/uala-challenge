<?php

namespace App\Contexts\Timeline\Domain\Repositories;

use App\Contexts\Timeline\Domain\Models\Timeline;
use App\Contexts\Timeline\Domain\Models\TimelineEntry;

interface TimelineRepositoryInterface
{
    public function addEntry(string $userId, TimelineEntry $entry): void;
    public function getTimeline(string $userId, int $page = 1, int $pageSize = 20): Timeline;
    public function deleteEntry(string $userId, string $tweetId): void;
}
