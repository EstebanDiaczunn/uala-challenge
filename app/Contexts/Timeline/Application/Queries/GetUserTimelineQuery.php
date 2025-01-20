<?php

namespace App\Contexts\Timeline\Application\Queries;

use App\Contexts\Timeline\Domain\DTOs\IndexTimelineDTO;
use App\Contexts\Timeline\Domain\Models\Timeline;
use App\Contexts\Timeline\Domain\Repositories\TimelineRepositoryInterface;
use App\Contexts\User\Domain\Exceptions\UserNotFoundException;
use App\Contexts\User\Domain\Repositories\UserRepositoryInterface;

class GetUserTimelineQuery
{
    public function __construct(
        private readonly TimelineRepositoryInterface $timelineRepository,
        private readonly UserRepositoryInterface $userRepository
    ) {
    }

    public function execute(IndexTimelineDTO $dto): Timeline
    {
        if (!$this->userRepository->existsById($dto->userId)) {
            throw new UserNotFoundException($dto->userId);
        }

        return $this->timelineRepository->getTimeline($dto->userId, $dto->page, $dto->perPage);
    }
}