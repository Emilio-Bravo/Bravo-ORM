<?php

namespace Core\Http;

class Server
{
    public static function referer()
    {
        return $_SERVER['HTTP_REFERER'];
    }

    public static function method()
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    public static function host()
    {
        return $_SERVER['HTTP_HOST'];
    }

    public static function uri()
    {
        $path = $_SERVER['REQUEST_URI'];
        $position = strpos($path, '?');
        if ($position === false) return $path;
        return substr($path, 0, $position);
    }

    public static function server()
    {
        return (object) $_SERVER;
    }

    public static function get(string $key)
    {
        if (in_array($key, $_SERVER)) return $_SERVER[$key];
    }
}
