<?php

namespace Core\Http;

class Files
{
    public static function all()
    {
        return (object) $_FILES;
    }

    public static function get($key)
    {
        return (object) $_FILES[$key];
    }
}
