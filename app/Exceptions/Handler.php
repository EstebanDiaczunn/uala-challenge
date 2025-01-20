<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    public function render($request, Throwable $e): JsonResponse|Response
    {
        if ($e instanceof \DomainException) {
            // Use a default HTTP status code if getCode() returns 0
            $statusCode = $e->getCode() ?: Response::HTTP_BAD_REQUEST;

            return new JsonResponse([
                'error' => $e->getMessage(),
            ], $statusCode);
        }

        return parent::render($request, $e);
    }

    public function register(): void
    {
        // Remove this since we're handling it in render()
        // The register method can be used for other exception handling setup
    }

}
