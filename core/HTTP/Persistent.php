<?php

namespace Core\Http;

class Persistent
{
    public function __construct()
    {
        self::init();
    }

    public static function init()
    {
        if (session_status() != PHP_SESSION_ACTIVE) session_start();
    }

    public static function create(string $key, $value): void
    {
        self::init();
        $_SESSION[$key] = $value;
    }

    public static function destroy(string $key): void
    {
        self::init();
        if (isset($_SESSION[$key])) unset($_SESSION[$key]);
    }

    public static function set_value(string $key, $value): void
    {
        self::init();
        $_SESSION[$key] = $value;
    }

    public static function get($key)
    {
        self::init();
        return $_SESSION[$key] ?? false;
    }

    public function has($key)
    {
        self::init();
        return isset($_SESSION[$key]);
    }

    public static function push_value(string $key, $value): void
    {
        self::init();
        $_SESSION[$key][] = $value;
    }

    public static function under_push(string $session_key, string $key, $value)
    {
        self::init();
        $_SESSION[$session_key][][$key] = $value;
    }
}
