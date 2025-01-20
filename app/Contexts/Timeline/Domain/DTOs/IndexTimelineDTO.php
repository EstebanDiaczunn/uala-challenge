<?php

namespace App\Contexts\Timeline\Domain\DTOs;

use App\Contexts\Timeline\Infrastructure\Http\Requests\IndexTimelineRequest;


class IndexTimelineDTO
{
    public function __construct(
        public readonly string $userId,
        public readonly int $page = 1,
        public readonly int $perPage = 10
    ) {
    }

    public static function fromRequest(IndexTimelineRequest $request): self
    {
        $userId = $request->user->id ?? $request->header('X-User-ID') ?? throw new \InvalidArgumentException('User ID not provided', 404);
        $data = $request->validated();
        return new self(
            $userId,
            $data['page'] ?? 1,
            $data['per_page'] ?? 10
        );
    }
}

