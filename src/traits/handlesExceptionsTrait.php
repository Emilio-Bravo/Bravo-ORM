<?php

namespace Bravo\ORM;

trait handlesExceptions
{
    /**
     * Shows a message
     */
    public function debug(string $message): void
    {
        exit(PHP_EOL . '<br><pre>' . htmlspecialchars($message) . '</pre>');
    }
}
