<?php

namespace Core\Support\Formating;

class Str
{
    public static function regex(string $str): string
    {
        return str_pad($str, strlen($str) + 2, '/', STR_PAD_BOTH);
    }

    public static function sprf(string $str, ...$values): string
    {
        return sprintf($str, $values);
    }
}