<?php

namespace App\Contexts\User\Infrastructure\Providers;

use App\Contexts\User\Domain\Repositories\UserRepositoryInterface;
use App\Contexts\User\Infrastructure\Persistence\EloquentUserRepository;
use Illuminate\Support\ServiceProvider;

class UserServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(UserRepositoryInterface::class, EloquentUserRepository::class);
    }
}
