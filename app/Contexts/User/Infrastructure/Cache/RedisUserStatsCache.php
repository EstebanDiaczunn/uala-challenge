<?php

namespace App\Contexts\User\Infrastructure\Cache;

use App\Contexts\User\Domain\Repositories\UserRepositoryInterface;
use Illuminate\Redis\RedisManager;

class RedisUserStatsCache implements UserStatsCacheInterface
{
    private const CACHE_TTL = 3600; // 1 hora¿

    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly RedisManager $redis
    ) {
    }

    /**
     * Devuelve el numeero de seguidores de un usuario dada su ID.
     * Si no esta en redis, se refresca.
     *
     * @param string $userId
     *
     * @return int
     */
    public function getFollowersCount(string $userId): int
    {
        $key = UserStatsKeys::getFollowersKey($userId);
        $cachedCount = $this->getCachedValue($key);

        // Si no est  en la cach , se refresca
        if ($cachedCount === null) {
            return $this->refreshFollowersCount($userId);
        }

        // Convertimos el valor a entero para asegurarnos de que sea un n mero
        return (int) $cachedCount;
    }

    /**
     * Devuelve el numeero de seguidos de un usuario dada su ID.
     * Si no esta en redis, se refresca.
     *
     * @param string $userId
     *
     * @return int
     */
    public function getFollowingCount(string $userId): int
    {
        $key = UserStatsKeys::getFollowingKey($userId);
        $cachedCount = $this->getCachedValue($key);

        if ($cachedCount === null) {
            return $this->refreshFollowingCount($userId);
        }

        return (int) $cachedCount;
    }

    public function incrementFollowersCount(string $userId): void
    {
        $key = UserStatsKeys::getFollowersKey($userId);

        if (!$this->redis->exists($key)) {
            // Importante: ACA contamos antes de que la nueva relación se refleje
            $count = $this->userRepository->countFollowers($userId);
            $this->redis->setex($key, self::CACHE_TTL, $count);
        }

        $this->redis->incr($key);
    }

    public function incrementFollowingCount(string $userId): void
    {
        $key = UserStatsKeys::getFollowingKey($userId);

        if (!$this->redis->exists($key)) {
            $count = $this->userRepository->countFollowing($userId);
            $this->redis->setex($key, self::CACHE_TTL, $count);
        }

        $this->redis->incr($key);
    }

    public function decrementFollowersCount(string $userId): void
    {
        $key = UserStatsKeys::getFollowersKey($userId);

        if (!$this->redis->exists($key)) {
            // Aquí es importante: contamos mientras la relación aún existe
            $count = $this->userRepository->countFollowers($userId);
            $this->redis->setex($key, self::CACHE_TTL, $count);
        }

        $currentCount = (int) $this->getCachedValue($key);

        // Solo decrementamos si hay seguidores
        if ($currentCount > 0) {
            $this->redis->decr($key);
        }
    }

    private function refreshFollowersCount(string $userId): int
    {
        $count = $this->userRepository->countFollowers($userId);
        $key = UserStatsKeys::getFollowersKey($userId);

        $this->redis->setex($key, self::CACHE_TTL, $count);

        return $count;
    }

    public function decrementFollowingCount(string $userId): void
    {
        $key = UserStatsKeys::getFollowingKey($userId);

        if (!$this->redis->exists($key)) {
            $count = $this->userRepository->countFollowing($userId);
            $this->redis->setex($key, self::CACHE_TTL, $count);
        }

        $currentCount = (int) $this->getCachedValue($key);

        if ($currentCount > 0) {
            $this->redis->decr($key);
        }
    }

    private function refreshFollowingCount(string $userId): int
    {
        $count = $this->userRepository->countFollowing($userId);
        $key = UserStatsKeys::getFollowingKey($userId);

        $this->redis->setex($key, self::CACHE_TTL, $count);

        return $count;
    }

    private function getCachedValue(string $key): ?string
    {
        try {
            return $this->redis->get($key);
        } catch (\Exception $e) {
            logger('No se pudo obtener el valor del caché: ' . $e->getMessage());
            return null;
        }
    }
}
