<?php

namespace Bravo\ORM;

use Exception;
use Bravo\ORM\ExceptionInterface;

class noConnectionException extends Exception implements ExceptionInterface
{
    public function __construct()
    {
        parent::__construct('There is no connection in the database');
    }
    public function errorMessage()
    {
        return "noConnectionException: {$this->getMessage()}";
    }
}