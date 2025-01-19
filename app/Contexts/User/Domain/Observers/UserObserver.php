<?php

namespace App\Contexts\User\Domain\Observers;

use App\Contexts\User\Domain\Models\User;
use Godruoyi\Snowflake\Snowflake;

class UserObserver
{
    public function creating(User $model)
    {
        $model->id = static::generateId();
    }

    public static function generateId(): string
    {
        return app(Snowflake::class)->id();
    }
}
