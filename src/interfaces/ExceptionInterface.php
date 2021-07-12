<?php

namespace Bravo\ORM;

/**
 * Makes an implementation of base methods of an Exception
 */

interface ExceptionInterface
{
    public function __construct();
    public function errorMessage();
}
