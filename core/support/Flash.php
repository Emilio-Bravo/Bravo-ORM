<?php

namespace Core\Support;

use Core\Http\Persistent;

class Flash
{

    private static $session_key;

    public static function create($key, $value)
    {
        self::$session_key = $key;
        Persistent::create($key, $value);
        self::change_status();
    }

    public static function change_status()
    {
        Persistent::create(self::$session_key . '_quit', true);
    }

    public static function enable()
    {
        foreach ($_SESSION as $key => $session) {
            
            $target = str_replace('_quit', '', $key);
            
            if (preg_match('/_quit/', $key) && Persistent::get($target) != null) {
                Persistent::destroy($target);
                Persistent::destroy($key);
            }
        }
    }
}

