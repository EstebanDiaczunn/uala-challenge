<?php

namespace App\Contexts\Tweet\Infrastructure\Http\Controller;

use App\Contexts\Tweet\Application\Commands\CreateTweetCommand;
use App\Contexts\Tweet\Domain\DTOs\TweetDTO;
use App\Contexts\Tweet\Infrastructure\Http\Requests\CreateTweetRequest;
use App\Shared\Infraestructure\Http\Controllers\ApiController;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class TweetController extends ApiController
{
    public function __construct(
        private readonly CreateTweetCommand $createTweetCommand
    ) {
    }

    /**
     * @OA\Post(
     *     path="/api/v1/tweets",
     *     tags={"Tweet Management"},
     *     summary="Crear un nuevo tweet",
     *     description="Crea un nuevo tweet y lo publica en la plataforma.",
     *     security={
     *         {"user_id_header": {}}
     *     },
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"content"},
     *             @OA\Property(
     *                 property="content", 
     *                 type="string", 
     *                 example="¡Este es mi primer tweet!",
     *                 description="Contenido del tweet (máximo 280 caracteres)"
     *             )
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="X-User-ID",
     *         in="header",
     *         required=true,
     *         description="ID del usuario autenticado, enviado como encabezado",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Tweet creado exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message", 
     *                 type="string", 
     *                 example="Tweet created successfully"
     *             ),
     *             @OA\Property(
     *                 property="id", 
     *                 type="string", 
     *                 example="507f1f77bcf86cd799439011"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Error de validación",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message", 
     *                 type="string", 
     *                 example="The given data was invalid."
     *             ),
     *             @OA\Property(
     *                 property="errors", 
     *                 type="object",
     *                 @OA\Property(
     *                     property="content",
     *                     type="array",
     *                     @OA\Items(
     *                         type="string",
     *                         example="El contenido del tweet no puede exceder los 280 caracteres."
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Usuario no autenticado",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message", 
     *                 type="string", 
     *                 example="Usuario no autenticado"
     *             )
     *         )
     *     )
     * )
     */
    public function store(CreateTweetRequest $request): JsonResponse
    {
        $dto = TweetDTO::fromRequest($request);

        $tweet = $this->createTweetCommand->execute($dto);

        return new JsonResponse(
            ['message' => 'Tweet created successfully', 'id' => $tweet->getId()],
            Response::HTTP_CREATED
        );
    }
}