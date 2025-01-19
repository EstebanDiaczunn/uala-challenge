<?php

namespace Tests\Feature\Contexts\User\Infrastructure\Http\Controllers;

use App\Contexts\User\Application\Commands\FollowUserCommand;
use App\Contexts\User\Domain\DTOs\FollowUserDTO;
use App\Contexts\User\Domain\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Redis\RedisManager;
use Illuminate\Support\Facades\Redis;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;
use App\Contexts\User\Domain\Repositories\UserRepositoryInterface;
use App\Contexts\User\Infrastructure\Cache\UserStatsCacheInterface;

class UnfollowUserTest extends TestCase
{
    use RefreshDatabase;

    private UserRepositoryInterface $userRepository;
    private UserStatsCacheInterface $statsCache;
    private FollowUserCommand $followUserCommand;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userRepository = app(UserRepositoryInterface::class);
        $this->statsCache = app(UserStatsCacheInterface::class);
        $this->followUserCommand = app(FollowUserCommand::class);
        Redis::flushall();
    }

    public function test_user_can_unfollow_another_user(): void
    {
        // Arrange
        $follower = User::factory()->create();
        $target = User::factory()->create();

        // Establecemos la relación inicial usando el comando
        $this->followUserCommand->execute(new FollowUserDTO(
            followerId: $follower->id,
            targetUserId: $target->id
        ));

        // Verificamos que la relación se estableció correctamente
        $this->assertEquals(1, $this->statsCache->getFollowersCount($target->id));
        $this->assertEquals(1, $this->statsCache->getFollowingCount($follower->id));

        // Act
        $response = $this->deleteJson(
            "/api/v1/users/{$target->id}/unfollow",
            [],
            ['X-User-ID' => $follower->id]
        );

        // Assert
        $response->assertStatus(Response::HTTP_OK)
            ->assertJson(['message' => 'User unfollowed successfully']);

        // Verifica que la relación haya sido eliminada
        $this->assertDatabaseMissing('follows', [
            'follower_id' => $follower->id,
            'followed_id' => $target->id
        ]);

        // Verifica los contadores en Redis
        $this->assertEquals(0, $this->statsCache->getFollowersCount($target->id));
        $this->assertEquals(0, $this->statsCache->getFollowingCount($follower->id));
    }

    public function test_cannot_unfollow_self(): void
    {
        // Arrange
        $user = User::factory()->create();

        // Act
        $response = $this->deleteJson(
            "/api/v1/users/{$user->id}/unfollow",
            [], // Cuerpo vacío
            ['X-User-ID' => $user->id] // El usuario intenta dejar de seguirse a sí mismo
        );

        // Assert
        $response->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJson(['message' => 'Cannot unfollow yourself.']);
    }

    public function test_cannot_unfollow_if_not_following(): void
    {
        // Arrange
        $follower = User::factory()->create();
        $target = User::factory()->create();

        // Act
        $response = $this->deleteJson(
            "/api/v1/users/{$target->id}/unfollow",
            [],
            ['X-User-ID' => $follower->id] // Usuario no está siguiendo al target
        );

        // Assert
        $response->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJson(['message' => "Already not following user {$target->id}."]);
    }
}
