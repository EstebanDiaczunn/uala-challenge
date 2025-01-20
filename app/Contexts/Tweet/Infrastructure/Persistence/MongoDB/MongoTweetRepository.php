<?php

namespace App\Contexts\Tweet\Infrastructure\Persistence\MongoDB;

use App\Contexts\Tweet\Domain\Models\Tweet;
use App\Contexts\Tweet\Domain\Repositories\TweetRepositoryInterface;
use App\Contexts\Tweet\Domain\ValueObjects\TweetContent;
use MongoDB\Client;
use MongoDB\Collection;

class MongoTweetRepository implements TweetRepositoryInterface
{
    private Collection $collection;

    public function __construct(Client $client)
    {
        $database = config('mongodb.database');
        $this->collection = $client->selectDatabase($database)->selectCollection('tweets');
    }

    public function save(Tweet $tweet): Tweet
    {
        $data = [
            'user_id' => $tweet->getUserId(),
            'content' => $tweet->getContent(),
            'created_at' => $tweet->getCreatedAt()
        ];

        $result = $this->collection->insertOne($data);

        if (!$result->getInsertedId()) {
            //cammbiar errorr
            throw new \RuntimeException('No se pudo guardar el tweet');
        }

        return new Tweet(
            $tweet->getUserId(),
            new TweetContent($tweet->getContent()),
            $result->getInsertedId()
        );
    }
}