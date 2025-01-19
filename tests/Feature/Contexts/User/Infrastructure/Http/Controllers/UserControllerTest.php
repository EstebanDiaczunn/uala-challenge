<?php

namespace Tests\Feature\Contexts\User\Infrastructure\Http\Controllers;

use App\Contexts\User\Domain\Observers\UserObserver;
use Artisan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Ejecuta las migraciones y seeders antes de cada prueba
        Artisan::call('migrate');
    }

    /** @test */
    public function it_should_create_a_new_user_successfully()
    {
        // Arrange: Preparamos los datos
        $data = [
            'username' => 'johndoe',
            'display_name' => 'John Doe',
        ];

        // Act: Simulamos una solicitud
        $response = $this->postJson('/api/v1/users', $data);

        // Assert: Verificamos la respuesta
        $response->assertStatus(201)
            ->assertJson([
                'message' => 'User created successfully',
                'user' => [
                    'username' => 'johndoe',
                    'display_name' => 'John Doe',
                ],
            ]);

        // Verificamos que el usuario se haya guardado en la base de datos
        $this->assertDatabaseHas('users', [
            'username' => 'johndoe',
            'display_name' => 'John Doe',
        ]);
    }

    /** @test */
    public function it_should_return_user_not_found()
    {
        // Arrange: sin "token"
        $existentId = UserObserver::generateId();

        // Act: Simulamos una solicitud para obtener un usuario existente
        $response = $this->getJson("/api/v1/users/{$existentId}");

        // Assert: Verificamos el mensaje de error
        $response->assertStatus(404);
    }
}
