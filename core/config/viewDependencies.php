<?php

/**
 * Do not change array keys, 
 * unless youre shure that your 
 * application is using the same keys!
 */

return [
    '_session' => \Core\Http\Persistent::class,
    '_flash' => \Core\Support\Flash::class,
    '_view' => \Core\Client\ViewHelper::class
];
