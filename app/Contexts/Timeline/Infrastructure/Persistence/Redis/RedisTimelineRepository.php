<?php

namespace App\Contexts\Timeline\Infrastructure\Persistence\Redis;

use App\Contexts\Timeline\Domain\Models\Timeline;
use App\Contexts\Timeline\Domain\Models\TimelineEntry;
use App\Contexts\Timeline\Domain\Repositories\TimelineRepositoryInterface;
use Illuminate\Redis\RedisManager;

class RedisTimelineRepository implements TimelineRepositoryInterface
{
    private const TIMELINE_KEY_PREFIX = 'timeline:';
    private const TTL_DAYS = 7;

    private const MAX_TWEETS = 100;

    public function __construct(
        private readonly RedisManager $redis
    ) {
    }

    public function addEntry(string $userId, TimelineEntry $entry): void
    {
        $key = $this->getTimelineKey($userId);

        // Usamos microtime para tener más precisión en el ordenamiento
        // microtime(true) nos da un timestamp con microsegundos como float
        $score = (float) microtime(true);

        \Log::info('Agregando entrada al timeline', [
            'key' => $key,
            'content' => $entry->getContent(),
            'score' => $score,
            'created_at' => $entry->getCreatedAt()->format('Y-m-d H:i:s.u')
        ]);

        // Agregamos el entry al sorted set con el score
        $this->redis->zadd(
            $key,
            $score,
            json_encode([
                'tweet_id' => $entry->getTweetId(),
                'user_id' => $entry->getUserId(),
                'content' => $entry->getContent(),
                'created_at' => $entry->getCreatedAt()->format('Y-m-d H:i:s'),
                '_score' => $score
            ])
        );

        // Configuramos el TTL
        $this->redis->expire($key, self::TTL_DAYS * 24 * 60 * 60);

        // Mantenemos el límite de tweets
        $this->redis->zremrangebyrank($key, 0, -(self::MAX_TWEETS + 1));
    }

    public function getTimeline(string $userId, int $page = 1, int $pageSize = 20): Timeline
    {
        $key = $this->getTimelineKey($userId);
        $start = ($page - 1) * $pageSize;
        $end = $start + $pageSize - 1;

        // Verificar si la clave existe
        if (!$this->redis->exists($key)) {
            \Log::warning('No se encontró timeline para el usuario', [
                'user_id' => $userId,
                'key' => $key
            ]);
            return new Timeline($userId, $pageSize);
        }

        $entries = $this->redis->zrevrange($key, $start, $end);

        $timeline = new Timeline($userId, $pageSize);
        foreach ($entries as $entry) {
            try {
                $data = json_decode($entry, true);

                if (json_last_error() !== JSON_ERROR_NONE) {
                    continue;
                }

                $timeline->addEntry(new TimelineEntry(
                    $data['tweet_id'],
                    $data['user_id'],
                    $data['content'],
                    new \DateTime($data['created_at']),
                    $data['metadata'] ?? []
                ));
            } catch (\Exception $e) {
                \Log::error('Error procesando entrada de timeline', [
                    'error' => $e->getMessage(),
                    'entry' => $entry
                ]);
            }
        }

        return $timeline;
    }

    private function getTimelineKey(string $userId): string
    {
        return self::TIMELINE_KEY_PREFIX . $userId;
    }

    public function deleteEntry(string $userId, string $tweetId): void
    {
        $key = $this->getTimelineKey($userId);
        $this->redis->zrem($key, $tweetId);
    }
}