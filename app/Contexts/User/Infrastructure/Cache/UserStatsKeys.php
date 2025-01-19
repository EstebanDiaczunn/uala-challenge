<?php

namespace App\Contexts\User\Infrastructure\Cache;

class UserStatsKeys
{
    private const CONTEXT = 'redis_keys.user';

    public static function getFollowersKey(string $userId): string
    {
        $key = config(self::CONTEXT . '.followers_count');
        return self::buildUserKey($key, $userId);
    }

    public static function getFollowingKey(string $userId): string
    {
        $key = config(self::CONTEXT . '.following_count');
        return self::buildUserKey($key, $userId);
    }

    private static function buildUserKey(string $key, string $userId): string
    {
        return sprintf($key, $userId);
    }
}
