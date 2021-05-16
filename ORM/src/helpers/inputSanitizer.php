<?php

namespace Bravo\ORM;

class inputSanitizer
{

    public const REPLACEMENT = "";

    public static function sanitize($input)
    {
        return strip_tags(trim($input));
    }
    public static function sanitizeTags($input)
    {
        return strip_tags($input);
    }
    public static function sanitizeSpaces($input)
    {
        return trim($input);
    }
    public static function sanitizeAllSpaces($input)
    {
        return self::sanitizeSpaces(preg_replace('/\s/', self::REPLACEMENT, $input));
    }
    public static function sanitizeNumbers($input)
    {
        return self::sanitizeSpaces(preg_replace('/[0-9]/', self::REPLACEMENT, $input));
    }
    public static function sanitizeWord($coincidence, $input)
    {
        return self::sanitizeSpaces(preg_replace('/' . $coincidence . '/i', self::REPLACEMENT, $input));
    }
    public static function sanitizeLetters($input)
    {
        return self::sanitizeSpaces(preg_replace('/[a-zA-Z]/', self::REPLACEMENT, $input));
    }
    public static function sanitizeCharacters($input)
    {
        return self::sanitizeSpaces(preg_replace('/\D/', self::REPLACEMENT, $input));
    }
    public static function sanitizeDigits($input)
    {
        return self::sanitizeSpaces(preg_replace('/\d/', self::REPLACEMENT, $input));
    }
    public static function sanitizeImpurities($input)
    {
        return self::sanitizeSpaces(preg_replace('/\W/', self::REPLACEMENT, $input));
    }
    public static function sanitizeUpperCase($input)
    {
        return self::sanitizeSpaces(preg_replace("/[A-Z]/", self::REPLACEMENT, $input));
    }
    public static function sanitizeLowerCase($input)
    {
        return self::sanitizeSpaces(preg_replace("/[a-z]/", self::REPLACEMENT, $input));
    }
    public static function sanitizeXSS($input)
    {
        return self::sanitizeSpaces(preg_replace('/(on|ON)+(\w)+?(=)+?("")/', self::REPLACEMENT, $input));
    }
    public static function sanitizeScript($input)
    {
        return self::sanitizeSpaces(preg_replace('', self::REPLACEMENT, $input));
    }
    public static function sanitizeEmail($input)
    {
        return self::sanitizeSpaces(preg_replace('/\b[A-Za-z0-9._%@+-]+@[a-zA-z0-9]+\.[a-zA-Z]{2,6}\b/', self::REPLACEMENT, $input));
    }
    public static function sanitizeURL($input)
    {
        return self::sanitizeSpaces(preg_replace('/^(http|https)+:\/\/+([\w\S\d])+\.([\w\d])+?([a-z])+$/', self::REPLACEMENT, $input));
    }
    public static function santizeLastCharacter($input, $char = ',')
    {
        return self::sanitize(rtrim($input, $char));
    }
}
