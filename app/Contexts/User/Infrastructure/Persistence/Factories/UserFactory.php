<?php

namespace App\Contexts\User\Infrastructure\Persistence\Factories;

use App\Contexts\User\Domain\Models\User;
use App\Contexts\User\Domain\Observers\UserObserver;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    /**
     * El nombre del modelo asociado con esta factory.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define el estado por defecto para los usuarios.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'id' => UserObserver::generateId(),
            'username' => $this->faker->unique()->userName,
            'display_name' => $this->faker->name,
        ];
    }
}
