<?php

namespace App\Contexts\Tweet\Infrastructure\Providers;

use App\Contexts\Tweet\Domain\Events\Publishers\TweetEventPublisherInterface;
use App\Contexts\Tweet\Domain\Repositories\TweetRepositoryInterface;
use App\Contexts\Tweet\Infrastructure\Events\Publishers\RabbitMQTweetPublisher;
use App\Contexts\Tweet\Infrastructure\Persistence\MongoDB\MongoTweetRepository;
use Illuminate\Support\ServiceProvider;

class TweetServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(TweetRepositoryInterface::class, MongoTweetRepository::class);
        $this->app->bind(TweetEventPublisherInterface::class, RabbitMQTweetPublisher::class);
    }
}
