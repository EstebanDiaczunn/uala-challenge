<?php

namespace App\Contexts\User\Domain\DTOs;

use App\Contexts\User\Infrastructure\Http\Requests\SearchUserRequest;
use Illuminate\Support\Facades\Request;

class SearchUserDTO
{
    public function __construct(
        public readonly ?string $username,
        public readonly ?int $perPage = 10
    ) {
    }

    public static function fromRequest(array $data): self
    {
        return new self(
            username: $data['username'] ?? '',
            perPage: $data['per_page'] ?? 10
        );
    }
}

