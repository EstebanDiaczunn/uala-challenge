<?php

namespace App\Contexts\User\Domain\Repositories;

use App\Contexts\User\Domain\Models\User;

interface UserRepositoryInterface
{
    public function create(array $data): User;
    public function findById(string $id): ?User;
    public function existsByUsername(string $username): bool;
}
