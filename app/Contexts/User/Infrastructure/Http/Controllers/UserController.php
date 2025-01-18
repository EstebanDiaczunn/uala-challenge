<?php

namespace App\Contexts\User\Infrastructure\Http\Controllers;

use App\Contexts\User\Application\Commands\CreateUserCommand;
use App\Contexts\User\Domain\DTOs\CreateUserDTO;
use App\Contexts\User\Infrastructure\Http\Requests\CreateUserRequest;
use App\Contexts\User\Infrastructure\Http\Resources\UserResource;
use App\Shared\Infraestructure\Http\Controllers\ApiController;
use Illuminate\Http\JsonResponse;

class UserController extends ApiController
{
    public function __construct(
        private readonly CreateUserCommand $createUserCommand
    ) {}

    /**
     * @OA\Post(
     *     path="/api/users",
     *     tags={"User Management"},
     *     summary="Create a new user",
     *     description="Creates a new user and returns the user data.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"username"},
     *             @OA\Property(property="username", type="string", example="johndoe"),
     *             @OA\Property(property="display_name", type="string", example="John Doe")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User created successfully"),
     *             @OA\Property(property="user", type="object",
     *                 @OA\Property(property="id", type="string", example="550e8400-e29b-41d4-a716-446655440000"),
     *                 @OA\Property(property="username", type="string", example="johndoe"),
     *                 @OA\Property(property="display_name", type="string", example="John Doe")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(property="errors", type="object",
     *                 @OA\Property(property="username", type="array", @OA\Items(type="string", example="The username has already been taken."))
     *             )
     *         )
     *     )
     * )
     */

    public function store(CreateUserRequest $request): JsonResponse
    {
        $user = $this->createUserCommand->execute(CreateUserDTO::fromRequest($request->validated()));

        return response()->json([
            'message' => 'User created successfully',
            'user' => new UserResource($user),
        ], 201);
    }
}
