<?php

namespace Bravo\ORM;

use Bravo\ORM\BravoORM;

/**
 * Example model
 */
class Model
{
    use BravoORM;

    /**
     * Current database table
     * 
     * @var string
     */
    protected string $table = 'users';
}
