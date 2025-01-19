<?php

namespace Tests\Feature\Contexts\User\Infrastructure\Http\Controllers;

use App\Contexts\User\Domain\Models\User;
use App\Contexts\User\Domain\Repositories\UserRepositoryInterface;
use App\Contexts\User\Infrastructure\Cache\UserStatsCacheInterface;

use Artisan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class FollowUserTest extends TestCase
{
    use RefreshDatabase;

    private UserRepositoryInterface $userRepository;
    private UserStatsCacheInterface $statsCache;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userRepository = app(UserRepositoryInterface::class);
        $this->statsCache = app(UserStatsCacheInterface::class);

        Artisan::call('migrate');
    }

    public function test_user_can_follow_another_user(): void
    {
        // Arrange
        $follower = User::factory()->create();
        $target = User::factory()->create();

        // Act
        $response = $this->postJson(
            "/api/v1/users/{$target->id}/follow", // Ruta con el ID del target
            [], // Cuerpo vacío, ya que `target_user_id` se genera en `prepareForValidation`
            ['X-User-ID' => $follower->id] // Header con el ID del follower
        );

        // Assert
        $response->assertStatus(Response::HTTP_OK);

        // Verifica la relación en la base de datos
        $this->assertDatabaseHas('follows', [
            'follower_id' => $follower->id,
            'followed_id' => $target->id
        ]);

        // Verifica los contadores en Redis
        $this->assertEquals(1, $this->statsCache->getFollowersCount($target->id));
        $this->assertEquals(1, $this->statsCache->getFollowingCount($follower->id));
    }

    public function test_cannot_follow_self(): void
    {
        $user = User::factory()->create();

        $response = $this->postJson(
            "/api/v1/users/{$user->id}/follow",
            [],
            ['X-User-ID' => $user->id]
        );

        $response->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJson(['message' => 'Cannot follow yourself.']);
    }
}