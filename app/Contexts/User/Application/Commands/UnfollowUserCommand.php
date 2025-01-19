<?php

namespace App\Contexts\User\Application\Commands;

use App\Contexts\User\Domain\DTOs\UnfollowUserDTO;
use App\Contexts\User\Domain\Exceptions\{CannotUnfollowYourselfException, UserNotFollowingException};
use App\Contexts\User\Domain\Repositories\UserRepositoryInterface;
use App\Contexts\User\Infrastructure\Cache\UserStatsCacheInterface;
use DB;

class UnfollowUserCommand
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly UserStatsCacheInterface $statsCache
    ) {
    }

    /**
     * Otras validaciones que podría tener esta funcionalidad:
     *
     * - Verificar si existe un bloqueo entre los usuarios (tabla `blocks`).
     * - Implementar límites para evitar que un usuario abuse de la funcionalidad.
     */
    public function execute(UnfollowUserDTO $dto): void
    {
        $this->validateUnfollowAction($dto);

        DB::transaction(function () use ($dto): void {
            // Primero actualizamos los contadores en caché
            $this->statsCache->decrementFollowersCount($dto->targetUserId);
            $this->statsCache->decrementFollowingCount($dto->followerId);

            $this->userRepository->removeFollower($dto->followerId, $dto->targetUserId);
        });
    }

    private function validateUnfollowAction(UnfollowUserDTO $dto): void
    {
        self::checkIfIsSelfunfollow($dto->followerId, $dto->targetUserId);
        self::checkIsAlreadyUnfollowing($dto->followerId, $dto->targetUserId);
    }

    private static function checkIfIsSelfunfollow(string $followerId, string $targetUserId): void
    {
        if ($followerId === $targetUserId) {
            throw new CannotUnfollowYourselfException();
        }
    }

    private function checkIsAlreadyUnfollowing(string $followerId, string $targetUserId): void
    {
        if ($this->userRepository->isNotFollowing($followerId, $targetUserId)) {
            throw new UserNotFollowingException($targetUserId);
        }
    }
}