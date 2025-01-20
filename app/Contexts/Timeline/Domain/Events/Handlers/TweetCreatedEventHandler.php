<?php

namespace App\Contexts\Timeline\Domain\Events\Handlers;

use App\Contexts\Timeline\Domain\Models\TimelineEntry;
use App\Contexts\Timeline\Domain\Repositories\TimelineRepositoryInterface;
use App\Contexts\Tweet\Domain\Events\TweetCreatedEvent;
use App\Contexts\Tweet\Domain\Models\Tweet;
use App\Contexts\Tweet\Domain\ValueObjects\TweetContent;
use App\Contexts\User\Domain\Repositories\UserRepositoryInterface;
use MongoDB\BSON\ObjectId;
use DateTime;

class TweetCreatedEventHandler
{
    public function __construct(
        private readonly TimelineRepositoryInterface $timelineRepository,
        private readonly UserRepositoryInterface $userRepository
    ) {
    }

    public function handle($eventData): void
    {
        \Log::info('Recibiendo datos del evento', ['data' => $eventData]);

        // Reconstruimos el Tweet y el Event desde los datos recibidos
        $tweet = $this->reconstructTweet($eventData);

        try {
            // Obtener seguidores
            $followers = $this->userRepository->getFollowers($tweet->getUserId());
            \Log::info('Seguidores encontrados', ['count' => count($followers)]);

            // Crear entrada de timeline
            $entry = new TimelineEntry(
                $tweet->getId(),
                $tweet->getUserId(),
                $tweet->getContent(),
                $tweet->getCreatedAt()
            );

            // Actualizar timeline de cada seguidor
            foreach ($followers as $follower) {
                \Log::info('Actualizando timeline', [
                    'follower_id' => $follower->id,
                    'tweet_id' => $tweet->getId()
                ]);

                $this->timelineRepository->addEntry($follower->id, $entry);
            }

            \Log::info('Timeline actualizado exitosamente');
        } catch (\Exception $e) {
            \Log::error('Error procesando evento de timeline', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    private function reconstructTweet(array $data): Tweet
    {
        \Log::info('Reconstruyendo tweet desde datos', ['data' => $data]);

        return Tweet::reconstruct(
            $data['user_id'],
            new TweetContent($data['content']),
            new ObjectId($data['tweet_id'])
        );
    }
}