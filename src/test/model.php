<?php

namespace Bravo\ORM;

use Bravo\ORM\BravoORM;

/**
 * Example model
 */

class Model
{
    use BravoORM;

    protected static string $table = 'users'; //your table
}
