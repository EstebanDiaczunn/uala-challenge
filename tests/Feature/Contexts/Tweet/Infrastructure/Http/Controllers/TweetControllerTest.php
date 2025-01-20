<?php

namespace Tests\Feature\Contexts\Tweet\Infrastructure\Http\Controllers;

use Artisan;
error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);


use Tests\TestCase;
use App\Contexts\User\Domain\Models\User;
use App\Contexts\Tweet\Domain\Events\Publishers\TweetEventPublisherInterface;
use App\Contexts\Tweet\Domain\Repositories\TweetRepositoryInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use MongoDB\Client;
use Tests\Traits\MongoTestDatabase;
use Symfony\Component\HttpFoundation\Response;
use Mockery;

class TweetControllerTest extends TestCase
{
    use RefreshDatabase;
    use MongoTestDatabase;

    private TweetRepositoryInterface $tweetRepository;
    private Client $mongoClient;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tweetRepository = app(TweetRepositoryInterface::class);
        $this->mongoClient = $this->getMongoClient();
        $this->mockPublisher();

        $this->cleanMongoCollection('tweets');

        Artisan::call('migrate');
    }

    private function mockPublisher(): void
    {
        // Creamos un mock del publisher que no hará nada
        $publisherMock = Mockery::mock(TweetEventPublisherInterface::class);
        $publisherMock->shouldReceive('publish')->andReturn(null);

        // Reemplazamos la implementación real con nuestro mock
        $this->app->instance(TweetEventPublisherInterface::class, $publisherMock);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_user_can_create_tweet(): void
    {
        // Arrange
        $user = User::factory()->create();
        $tweetContent = "Este es un tweet de prueba";

        // Act
        $response = $this->postJson(
            '/api/v1/tweets',
            ['content' => $tweetContent],
            ['X-User-ID' => $user->id]
        );

        // Assert
        $response->assertStatus(Response::HTTP_CREATED)
            ->assertJsonStructure([
                'message',
                'id'
            ]);

        // Verificar que el tweet existe en MongoDB
        $tweet = $this->mongoClient
            ->selectDatabase(config('mongodb.database'))
            ->selectCollection('tweets')
            ->findOne([
                'user_id' => $user->id,
                'content' => $tweetContent
            ]);

        $this->assertNotNull($tweet);
        $this->assertEquals($user->id, $tweet['user_id']);
        $this->assertEquals($tweetContent, $tweet['content']);
    }

    public function test_cannot_create_tweet_with_invalid_length(): void
    {
        // Arrange
        $user = User::factory()->create();
        $longContent = str_repeat('a', 281);

        // Act
        $response = $this->postJson(
            '/api/v1/tweets',
            ['content' => $longContent],
            ['X-User-ID' => $user->id]
        );

        // Assert
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}