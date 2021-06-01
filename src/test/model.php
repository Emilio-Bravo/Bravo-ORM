<?php

namespace Bravo\ORM;

use Bravo\ORM\BravoORM;

/**
 * Example model
 */

class Model
{
    use BravoORM;

    protected static $table = 'users'; //your table
}
