<?php

namespace Tests\Feature\Contexts\Timeline\Infrastructure\Http\Controllers;

use App\Contexts\Timeline\Domain\Repositories\TimelineRepositoryInterface;
use App\Contexts\Tweet\Domain\Events\Publishers\TweetEventPublisherInterface;
use App\Contexts\User\Domain\Models\User;
use App\Contexts\User\Domain\Repositories\UserRepositoryInterface;
use Artisan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;
use Mockery;
use Tests\Traits\MongoTestDatabase;

class TimelineControllerTest extends TestCase
{
    use RefreshDatabase;
    use MongoTestDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Mockear el publisher para evitar llamadas reales a RabbitMQ
        $this->mockPublisher();

        // Limpiar MongoDB y la base de datos relacional antes de cada test
        $this->cleanMongoCollection('tweets');
        $this->artisan('migrate:fresh');
    }

    private function mockPublisher(): void
    {
        $publisherMock = Mockery::mock(TweetEventPublisherInterface::class);
        $publisherMock->shouldReceive('publish')->andReturn(null);
        $this->app->instance(TweetEventPublisherInterface::class, $publisherMock);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_user_can_see_followed_users_tweets(): void
    {
        // Arrange
        $follower = User::factory()->create();
        $followed = User::factory()->create();

        // El usuario comienza a seguir a followed
        $this->postJson(
            "/api/v1/users/{$followed->id}/follow",
            [],
            ['X-User-ID' => $follower->id]
        );

        // Obtenemos las dependencias necesarias
        $eventHandler = app(\App\Contexts\Timeline\Domain\Events\Handlers\TweetCreatedEventHandler::class);

        // Creamos los tweets y procesamos sus eventos manualmente
        $tweets = [
            'Primer tweet de prueba',
            'Segundo tweet de prueba',
            'Tercer tweet de prueba'
        ];

        foreach ($tweets as $content) {
            // Crear el tweet
            $response = $this->postJson(
                '/api/v1/tweets',
                ['content' => $content],
                ['X-User-ID' => $followed->id]
            );

            // Simular el trabajo del consumidor procesando el evento
            $tweetData = $response->json();
            $eventHandler->handle([
                'tweet_id' => $tweetData['id'],
                'user_id' => $followed->id,
                'content' => $content,
                'created_at' => now()->format('Y-m-d H:i:s')
            ]);
        }

        // Act - Ahora sí deberíamos ver los tweets en el timeline
        $response = $this->getJson('/api/v1/timeline', [
            'X-User-ID' => $follower->id
        ]);

        // Assert
        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonCount(3, 'data');

        $timelineData = $response->json('data');

        $this->assertEquals('Primer tweet de prueba', $timelineData[0]['content']);
        $this->assertEquals('Segundo tweet de prueba', $timelineData[1]['content']);
        $this->assertEquals('Tercer tweet de prueba', $timelineData[2]['content']);
    }
}
