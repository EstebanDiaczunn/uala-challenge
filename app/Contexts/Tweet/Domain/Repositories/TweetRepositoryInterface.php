<?php

namespace App\Contexts\Tweet\Domain\Repositories;

use App\Contexts\Tweet\Domain\Models\Tweet;

interface TweetRepositoryInterface
{
    public function save(Tweet $tweet): Tweet;

    // public function findById(string $id): ?Tweet;

    // public function findByUserId(string $userId, int $limit = 20, int $offset = 0): array;

    // public function delete(string $id): void;
}