<?php

namespace App\Contexts\User\Infrastructure\Persistence;

use App\Contexts\User\Domain\Models\User;
use App\Contexts\User\Domain\Repositories\UserRepositoryInterface;

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

    public function existsByUsername(string $username): bool
    {
        return User::where('username', $username)->exists();
    }
}
