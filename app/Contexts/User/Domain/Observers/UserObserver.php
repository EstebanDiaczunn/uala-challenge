<?php

namespace App\Contexts\User\Domain\Observers;

use App\Contexts\User\Domain\Models\User;
use Ramsey\Uuid\Uuid;

class UserObserver
{
    public function creating(User $model)
    {
        $model->id = Uuid::uuid4()->toString();
    }
}
