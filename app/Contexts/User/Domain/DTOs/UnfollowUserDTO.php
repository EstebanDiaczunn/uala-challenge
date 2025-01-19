<?php

namespace App\Contexts\User\Domain\DTOs;

use App\Contexts\User\Infrastructure\Http\Requests\FollowUserRequest;
use App\Contexts\User\Infrastructure\Http\Requests\UnfollowUserRequest;

class UnfollowUserDTO
{
    public function __construct(
        public readonly string $followerId,
        public readonly string $targetUserId
    ) {
    }

    public static function fromRequest(UnfollowUserRequest $request): self
    {
        return new self(
            followerId: $request->user->id ?? $request->header('X-User-ID') ?? throw new \InvalidArgumentException('User ID not provided', 404),
            targetUserId: $request->input('target_user_id')
        );
    }
}

