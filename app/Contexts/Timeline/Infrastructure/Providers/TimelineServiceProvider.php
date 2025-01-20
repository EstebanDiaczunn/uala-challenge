<?php

namespace App\Contexts\Timeline\Infrastructure\Providers;

use App\Contexts\Timeline\Domain\Repositories\TimelineRepositoryInterface;
use App\Contexts\Timeline\Infrastructure\Persistence\Redis\RedisTimelineRepository;
use Illuminate\Support\ServiceProvider;

class TimelineServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(TimelineRepositoryInterface::class, RedisTimelineRepository::class);
    }
}