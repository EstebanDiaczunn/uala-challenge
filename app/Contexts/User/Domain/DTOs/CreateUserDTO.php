<?php

namespace App\Contexts\User\Domain\DTOs;

class CreateUserDTO
{
    public function __construct(
        public readonly string $username,
        public readonly null|string $display_name,
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            username: $data['username'],
            display_name: $data['display_name'] ?? null,
        );
    }
}

