<?php

namespace App\Contexts\User\Application\Commands;

use App\Contexts\User\Domain\DTOs\CreateUserDTO;
use App\Contexts\User\Domain\Exceptions\UsernameAlreadyExistsException;
use App\Contexts\User\Domain\Models\User;
use App\Contexts\User\Domain\Repositories\UserRepositoryInterface;

class CreateUserCommand
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository
    ) {}

    public function execute(CreateUserDTO $dto): User
    {
        // Validamos que el username sea único
        if ($this->userRepository->existsByUsername($dto->username)) {
            throw new UsernameAlreadyExistsException($dto->username);
        }

        return $this->userRepository->create([
            'username' => $dto->username,
            'display_name' => $dto->display_name
        ]);
    }
}
