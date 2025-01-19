<?php

namespace App\Contexts\User\Application\Commands;

use App\Contexts\User\Domain\Exceptions\UserNotFoundException;
use App\Contexts\User\Domain\Models\User;
use App\Contexts\User\Domain\Repositories\UserRepositoryInterface;
use App\Contexts\User\Infrastructure\Cache\UserStatsCacheInterface;

class ShowUserCommand
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly UserStatsCacheInterface $statsCache
    ) {
    }

    public function execute(string $userId): User
    {
        $user = $this->userRepository->findById($userId);

        if (!$user) {
            throw new UserNotFoundException($userId);
        }

        $user->stats = $this->getUserStats($userId);
        return $user;
    }

    private function getUserStats(string $userId): array
    {
        return [
            'followers_count' => $this->statsCache->getFollowersCount($userId) ?? 0,
            'following_count' => $this->statsCache->getFollowingCount($userId) ?? 0,
        ];
    }
}
