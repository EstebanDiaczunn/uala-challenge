<?php

namespace App\Contexts\Timeline\Infrastructure\Http\Controllers;

use App\Contexts\Timeline\Application\Queries\GetUserTimelineQuery;
use App\Contexts\Timeline\Domain\DTOs\IndexTimelineDTO;
use App\Contexts\Timeline\Infrastructure\Http\Requests\IndexTimelineRequest;
use App\Contexts\Timeline\Infrastructure\Http\Resources\TimelineResource;
use App\Shared\Infraestructure\Http\Controllers\ApiController;
use Illuminate\Http\JsonResponse;

class TimelineController extends ApiController
{
    public function __construct(
        private readonly GetUserTimelineQuery $getUserTimelineQuery
    ) {
    }

    /**
     * @OA\Get(
     *     path="/api/v1/timeline",
     *     summary="Obtener el timeline del usuario",
     *     description="Obtiene los tweets más recientes de los usuarios seguidos, ordenados cronológicamente. El timeline se actualiza en tiempo real cuando los usuarios que sigues publican nuevos tweets.",
     *     operationId="getTimeline",
     *     tags={"Timeline"},
     *     security={{ "X-User-ID": {} }},
     *     @OA\Parameter(
     *         name="X-User-ID",
     *         in="header",
     *         required=true,
     *         description="ID del usuario autenticado, enviado como encabezado",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         required=false,
     *         description="Número de página para la paginación. El valor predeterminado es 1.",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         required=false,
     *         description="Cantidad de tweets por página. El valor predeterminado es 20.",
     *         @OA\Schema(type="integer", example=20)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Timeline obtenido exitosamente",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="tweet_id", type="string", example="507f1f77bcf86cd799439011"),
     *                     @OA\Property(property="user_id", type="string", example="123e4567-e89b-12d3-a456-426614174000"),
     *                     @OA\Property(property="content", type="string", example="¡Este es mi nuevo tweet!"),
     *                     @OA\Property(property="created_at", type="string", format="date-time", example="2025-01-20T06:12:27+00:00")
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="meta",
     *                 type="object",
     *                 @OA\Property(property="total", type="integer", example=45),
     *                 @OA\Property(property="page", type="integer", example=1),
     *                 @OA\Property(property="per_page", type="integer", example=20)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Usuario no autenticado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Usuario no autenticado")
     *         )
     *     )
     * )
     */
    public function index(IndexTimelineRequest $request): JsonResponse
    {
        $dto = IndexTimelineDTO::fromRequest($request);

        $timeline = $this->getUserTimelineQuery->execute($dto);

        return new JsonResponse(new TimelineResource($timeline));
    }
}