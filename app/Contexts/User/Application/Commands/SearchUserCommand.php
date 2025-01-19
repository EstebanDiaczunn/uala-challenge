<?php

namespace App\Contexts\User\Application\Commands;

use App\Contexts\User\Domain\DTOs\SearchUserDTO;
use App\Contexts\User\Domain\Repositories\UserRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class SearchUserCommand
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
    ) {
    }

    public function execute(SearchUserDTO $dto): LengthAwarePaginator
    {
        return $this->userRepository->findByUsername($dto->username, $dto->perPage);
    }
}