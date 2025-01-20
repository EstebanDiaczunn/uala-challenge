<?php

namespace App\Contexts\User\Infrastructure\Persistence;

use App\Contexts\User\Domain\Models\User;
use App\Contexts\User\Domain\Repositories\UserRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class EloquentUserRepository implements UserRepositoryInterface
{
    public function create(array $data): User
    {
        return User::create($data);
    }

    public function findById(string $id): ?User
    {
        return User::find($id);
    }

    public function findByUsername(string $username, int $perPage = 10): LengthAwarePaginator
    {
        return User::where('username', 'like', '%' . $username . '%')
            ->paginate($perPage);
    }

    public function existsByUsername(string $username): bool
    {
        return User::where('username', $username)->exists();
    }

    public function existsById(string $id): bool
    {
        return User::where('id', $id)->exists();
    }

    public function countFollowers(string $userId): int
    {
        return User::find($userId)->followers()->count() ?? 0;
    }

    public function countFollowing(string $userId): int
    {
        return User::find($userId)->following()->count() ?? 0;
    }

    public function addFollower(string $followerId, string $targetUserId): void
    {
        User::find($followerId)->following()->attach($targetUserId);
    }

    public function isAlreadyFollowing(string $followerId, string $targetUserId): bool
    {
        return User::find($followerId)->following()->where('followed_id', $targetUserId)->exists();
    }

    public function isNotFollowing(string $followerId, string $targetUserId): bool
    {
        return !$this->isAlreadyFollowing($followerId, $targetUserId);
    }

    public function removeFollower(string $followerId, string $targetUserId): void
    {
        User::find($followerId)->following()->detach($targetUserId);
    }

    public function getFollowers(string $userId): Collection
    {
        return User::find($userId)->followers()->get();
    }
}
