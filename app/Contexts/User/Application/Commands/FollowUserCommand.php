<?php

namespace App\Contexts\User\Application\Commands;

use App\Contexts\User\Domain\DTOs\FollowUserDTO;
use App\Contexts\User\Domain\Exceptions\{CannotFollowYourselfException, UserAlreadyFollowedException};
use App\Contexts\User\Domain\Repositories\UserRepositoryInterface;
use App\Contexts\User\Infrastructure\Cache\UserStatsCacheInterface;
use DB;

class FollowUserCommand
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly UserStatsCacheInterface $statsCache
    ) {
    }

    /**
     * Otras validaciones que podria tener esta funcionalidad:
     *
     * - Que el usuario que se va a seguir no este  bloqueado por el usuario que hace la solicitud
     * v - Que el usuario no este intentando seguir a alguien que ya est  siguiendo
     *
     * Algunas plataformas tienen limites de seguidores por usuario.
     */
    public function execute(FollowUserDTO $dto): void
    {
        $this->validateFollowAction($dto);

        DB::transaction(function () use ($dto): void {
            // Primero actualizamos los contadores en caché
            $this->statsCache->incrementFollowersCount($dto->targetUserId);
            $this->statsCache->incrementFollowingCount($dto->followerId);

            // Luego creamos la relación
            $this->userRepository->addFollower($dto->followerId, $dto->targetUserId);
        });
    }

    private function validateFollowAction(FollowUserDTO $dto): void
    {
        self::checkIfIsSelfFollow($dto->followerId, $dto->targetUserId);
        self::checkIsAlreadyFollowing($dto->followerId, $dto->targetUserId);
    }

    private static function checkIfIsSelfFollow(string $followerId, string $targetUserId): void
    {
        if ($followerId === $targetUserId) {
            throw new CannotFollowYourselfException();
        }
    }

    private function checkIsAlreadyFollowing(string $followerId, string $targetUserId): void
    {
        if ($this->userRepository->isAlreadyFollowing($followerId, $targetUserId)) {
            throw new UserAlreadyFollowedException($targetUserId);
        }
    }
}