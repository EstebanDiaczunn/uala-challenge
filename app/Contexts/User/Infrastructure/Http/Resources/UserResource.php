<?php

namespace App\Contexts\User\Infrastructure\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'display_name' => $this->display_name,
            'stats' => [
                'followers_count' => $this->stats['followers_count'] ?? null,
                'following_count' => $this->stats['following_count'] ?? null,
            ],
        ];
    }
}
