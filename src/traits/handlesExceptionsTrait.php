<?php

namespace Bravo\ORM;

trait handlesExceptions
{
    /**
     * Shows a message
     * 
     * @param string $message
     * @return void
     */
    public function debug(string $message): void
    {
        echo PHP_EOL . '<br><pre>' . htmlspecialchars($message) . '</pre>';
    }
}
