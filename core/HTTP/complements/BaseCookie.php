<?php

namespace Core\Http\Complements;

class BaseCookie
{
    public static function has(string $key): bool
    {
        return isset($_COOKIE[$key]);
    }

    public static function get(string $key)
    {
        return in_array($key, $_COOKIE) ? $_COOKIE[$key] : false;
    }

    public static function remove(string $key): void
    {
        new \Core\Http\Cookie($key, null, 0);

        if (in_array($key, $_COOKIE)) unset($_COOKIE[$key]);
    }
}
