<?php

namespace App\Contexts\User\Domain\Repositories;

use App\Contexts\User\Domain\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface UserRepositoryInterface
{
    public function create(array $data): User;
    public function findById(string $id): ?User;
    public function findByUsername(string $username, int $perPage = 10): LengthAwarePaginator;
    public function existsByUsername(string $username): bool;
    public function existsById(string $id): bool;
    public function countFollowers(string $userId): int;
    public function countFollowing(string $userId): int;

    public function addFollower(string $followerId, string $targetUserId): void;
    public function removeFollower(string $followerId, string $targetUserId): void;
    public function isAlreadyFollowing(string $followerId, string $targetUserId): bool;
    public function isNotFollowing(string $followerId, string $targetUserId): bool;

    public function getFollowers(string $userId): Collection;
}
