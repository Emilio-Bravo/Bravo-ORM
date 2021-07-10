<?php

namespace Core\Support;

class HttpSanitizer
{
    public static function sanitize_post()
    {
        foreach ($_POST as $key => $value) {
            $sanitized[$key] = trim(filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS));
        }
        return @$sanitized;
    }

    public static function sanitize_get()
    {
        foreach ($_GET as $key => $value) {
            $sanitized[$key] = trim(filter_input(INPUT_GET, $key, FILTER_SANITIZE_SPECIAL_CHARS));
        }
        return @$sanitized;
    }
}
