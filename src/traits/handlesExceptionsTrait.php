<?php

namespace Bravo\ORM;

trait handlesExceptions
{
    /**
     * Shows a message
     */
    public function debug(string $message): void
    {
        echo PHP_EOL . '<br><pre>' . htmlspecialchars($message) . '</pre>';
    }
}
