<?php

namespace App\Shared\Infraestructure;

use App\Contexts\User\Domain\Repositories\UserRepositoryInterface;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthMiddleware
{

    public function __construct(
        private readonly UserRepositoryInterface $userRepository
    ) {
    }

    public function handle(Request $request, Closure $next)
    {
        $headerUserId = $request->header('X-User-Id');

        if (!$headerUserId) {
            return response()->json([
                'error' => 'User ID not provided',
                'message' => 'Please provide a user ID in X-User-Id header'
            ], Response::HTTP_NOT_FOUND);
        }

        if (!$this->userRepository->existsById($headerUserId)) {
            return response()->json([
                'error' => 'Invalid user',
                'message' => 'The provided user ID does not exist'
            ], Response::HTTP_NOT_FOUND);
        }

        $request->attributes->set('user', $this->userRepository->findById($headerUserId));

        return $next($request);
    }
}
