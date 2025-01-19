<?php

namespace App\Contexts\User\Infrastructure\Http\Controllers;

use App\Contexts\User\Application\Commands\CreateUserCommand;
use App\Contexts\User\Application\Commands\FollowUserCommand;
use App\Contexts\User\Application\Commands\SearchUserCommand;
use App\Contexts\User\Application\Commands\ShowUserCommand;
use App\Contexts\User\Application\Commands\UnfollowUserCommand;
use App\Contexts\User\Domain\DTOs\CreateUserDTO;
use App\Contexts\User\Domain\DTOs\FollowUserDTO;
use App\Contexts\User\Domain\DTOs\SearchUserDTO;
use App\Contexts\User\Domain\DTOs\UnfollowUserDTO;
use App\Contexts\User\Infrastructure\Http\Requests\CreateUserRequest;
use App\Contexts\User\Infrastructure\Http\Requests\FollowUserRequest;
use App\Contexts\User\Infrastructure\Http\Requests\SearchUserRequest;
use App\Contexts\User\Infrastructure\Http\Requests\UnfollowUserRequest;
use App\Contexts\User\Infrastructure\Http\Resources\UserResource;
use App\Shared\Infraestructure\Http\Controllers\ApiController;
use Illuminate\Http\JsonResponse;
use Illuminate\Foundation\Http\FormRequest as Request;
use Symfony\Component\HttpFoundation\Response;

class UserController extends ApiController
{
    public function __construct(
        private readonly CreateUserCommand $createUserCommand,
        private readonly ShowUserCommand $showUserCommand,
        private readonly FollowUserCommand $followUserCommand,
        private readonly UnfollowUserCommand $unfollowUserCommand,
        private readonly SearchUserCommand $searchUserCommand
    ) {
    }

    /**
     * @OA\Post(
     *     path="/api/v1/users",
     *     tags={"User Management"},
     *     summary="Crear un nuevo usuario",
     *     description="Crea un nueevo usuario y devuelve su información.",
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

        return new JsonResponse([
            'message' => 'User created successfully',
            'user' => new UserResource($user),
        ], Response::HTTP_CREATED);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/users/{id}",
     *     summary="Obtener información de un usuario",
     *     description="Este endpoint permite obtener la información de un usuario especificado mediante su ID. Se requiere pasar un encabezado `X-User-ID` con el ID del usuario autenticado como token.",
     *     operationId="getUser",
     *     tags={"User Management"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID del usuario que se desea obtener",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="X-User-ID",
     *         in="header",
     *         required=true,
     *         description="ID del usuario autenticado, enviado como encabezado",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Información del usuario obtenida exitosamente",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="string", example="123e4567-e89b-12d3-a456-426614174000"),
     *             @OA\Property(property="username", type="string", example="johndoe"),
     *             @OA\Property(property="display_name", type="string", example="John Doe"),
     *             @OA\Property(
     *                 property="stats",
     *                 type="object",
     *                 @OA\Property(property="followers_count", type="integer", example=150),
     *                 @OA\Property(property="following_count", type="integer", example=100)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Usuario no encontrado",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="User not found.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Solicitud inválida",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Invalid request.")
     *         )
     *     )
     * )
     */

    public function show(string $id): JsonResponse
    {
        $user = $this->showUserCommand->execute($id);
        return new JsonResponse(new UserResource($user));
    }

    /**
     * @OA\Post(
     *     path="/api/v1/users/{targetUserId}/follow",
     *     summary="Seguir a un usuario",
     *     description="Permite que el usuario autenticado siga a otro usuario. El sistema actualiza los contadores de seguidores tanto en la base de datos como en caché.",
     *     operationId="followUser",
     *     tags={"User Management"},
     *     security={{ "X-User-ID": {} }},
     *     @OA\Parameter(
     *         name="targetUserId",
     *         in="path",
     *         required=true,
     *         description="ID del usuario a seguir",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="X-User-ID",
     *         in="header",
     *         required=true,
     *         description="ID del usuario autenticado, enviado como encabezado",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Usuario seguido exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User followed successfully"),
     *             @OA\Property(property="stats", type="object",
     *                 @OA\Property(property="followers_count", type="integer", example=42),
     *                 @OA\Property(property="following_count", type="integer", example=23)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Usuario no encontrado"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="No se puede seguir al mismo usuario"
     *     )
     * )
     */
    public function follow(FollowUserRequest $request): JsonResponse
    {
        $dto = FollowUserDTO::fromRequest($request);

        $this->followUserCommand->execute($dto);
        return new JsonResponse(['message' => 'User followed successfully'], Response::HTTP_OK);
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/users/{targetUserId}/unfollow",
     *     summary="Dejar de seguir a un usuario",
     *     description="Permite que el usuario autenticado deje de seguir a otro usuario. El sistema actualiza los contadores de seguidores tanto en la base de datos como en caché.",
     *     operationId="unfollowUser",
     *     tags={"User Management"},
     *     security={{ "X-User-ID": {} }},
     *     @OA\Parameter(
     *         name="X-User-ID",
     *         in="header",
     *         required=true,
     *         description="ID del usuario autenticado, enviado como encabezado",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="targetUserId",
     *         in="path",
     *         required=true,
     *         description="ID del usuario a dejar de seguir",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Usuario dejado de seguir exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User unfollowed successfully"),
     *             @OA\Property(property="stats", type="object",
     *                 @OA\Property(property="followers_count", type="integer", example=41),
     *                 @OA\Property(property="following_count", type="integer", example=22)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Usuario no encontrado"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Errores de validación",
     *         @OA\JsonContent(
     *             oneOf={
     *                 @OA\Schema(
     *                     @OA\Property(property="message", type="string", example="Cannot unfollow yourself.")
     *                 ),
     *                 @OA\Schema(
     *                     @OA\Property(property="message", type="string", example="User is not being followed.")
     *                 )
     *             }
     *         )
     *     )
     * )
     */

    public function unfollow(UnfollowUserRequest $request): JsonResponse
    {
        $dto = UnfollowUserDTO::fromRequest($request);

        $this->unfollowUserCommand->execute($dto);
        return new JsonResponse(['message' => 'User unfollowed successfully'], Response::HTTP_OK);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/users/search",
     *     summary="Buscar usuarios por nombre de usuario",
     *     description="Este endpoint permite buscar usuarios cuyo nombre de usuario coincida parcialmente con el valor proporcionado. Soporta paginación mediante el parámetro `per_page`.",
     *     operationId="searchUsers",
     *     tags={"User Management"},
     *     security={{ "X-User-ID": {} }},
     *     @OA\Parameter(
     *         name="username",
     *         in="query",
     *         required=true,
     *         description="El nombre de usuario o parte de él para buscar.",
     *         @OA\Schema(type="string", example="john")
     *     ),
     *     @OA\Parameter(
     *         name="X-User-ID",
     *         in="header",
     *         required=true,
     *         description="ID del usuario autenticado, enviado como encabezado",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         required=false,
     *         description="Cantidad de resultados por página. El valor predeterminado es 10.",
     *         @OA\Schema(type="integer", example=10)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de usuarios obtenida exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="users",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="string", format="uuid", example="123e4567-e89b-12d3-a456-426614174000"),
     *                     @OA\Property(property="username", type="string", example="johndoe"),
     *                     @OA\Property(property="display_name", type="string", example="John Doe"),
     *                     @OA\Property(property="followers_count", type="integer", example=150),
     *                     @OA\Property(property="following_count", type="integer", example=100)
     *                 )
     *             ),
     *             @OA\Property(property="current_page", type="integer", example=1),
     *             @OA\Property(property="total", type="integer", example=25),
     *             @OA\Property(property="per_page", type="integer", example=10)
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Solicitud inválida",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Invalid request.")
     *         )
     *     )
     * )
     */

    public function search(SearchUserRequest $request): JsonResponse
    {
        $dto = SearchUserDTO::fromRequest($request->validated());

        $result = $this->searchUserCommand->execute($dto);
        return response()->json([
            'users' => UserResource::collection($result)
        ]);
    }
}
