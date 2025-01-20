<?php

namespace App\Contexts\Timeline\Infrastructure\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TimelineResource extends JsonResource
{
    public function toArray($request): array
    {
        $entries = $this->resource->getEntries();

        return [
            'data' => array_values(array_map(function ($entry) {
                return [
                    'tweet_id' => $entry->getTweetId(),
                    'user_id' => $entry->getUserId(),
                    'content' => $entry->getContent(),
                    'created_at' => $entry->getCreatedAt()->format('c')
                ];
            }, $entries)),
            'meta' => [
                'total' => $this->resource->getTotalEntries(),
                'page' => (int) $request->get('page', 1),
                'per_page' => (int) $request->get('per_page', 20)
            ]
        ];
    }
}