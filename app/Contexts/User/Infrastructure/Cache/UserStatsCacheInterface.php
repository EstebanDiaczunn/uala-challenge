<?php

namespace App\Contexts\User\Infrastructure\Cache;

interface UserStatsCacheInterface
{
    public function getFollowersCount(string $userId): int;
    public function getFollowingCount(string $userId): int;
    public function incrementFollowersCount(string $userId): void;
    public function decrementFollowersCount(string $userId): void;
    public function incrementFollowingCount(string $userId): void;
    public function decrementFollowingCount(string $userId): void;
}
