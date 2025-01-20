<?php

namespace App\Contexts\Tweet\Application\Commands;

use App\Contexts\Tweet\Domain\DTOs\TweetDTO;
use App\Contexts\Tweet\Domain\Models\Tweet;
use App\Contexts\Tweet\Domain\Repositories\TweetRepositoryInterface;
use App\Contexts\Tweet\Domain\Events\Publishers\TweetEventPublisherInterface;
use App\Contexts\Tweet\Domain\ValueObjects\TweetContent;

class CreateTweetCommand
{
    public function __construct(
        private readonly TweetRepositoryInterface $tweetRepository,
        private readonly TweetEventPublisherInterface $eventPublisher
    ) {
    }

    public function execute(TweetDTO $dto): Tweet
    {
        //Usando el modelo
        $tweet = new Tweet(
            $dto->getUserId(),
            new TweetContent($dto->getContent())
        );

        // Persistir en MongoDB
        $savedTweet = $this->tweetRepository->save($tweet);

        // Publicar evento para procesamiento asíncrono
        // Esto actualizará los timelines de los seguidores
        $this->eventPublisher->publish($tweet->releaseEvents());

        return $savedTweet;
    }
}