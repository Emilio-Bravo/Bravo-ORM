<?php

namespace Bravo\ORM;

use Exception;
use Bravo\ORM\ExceptionInterface;

class statementException extends Exception implements ExceptionInterface
{
    public function __construct()
    {
        parent::__construct('Something went wrong with the SQL statment');
    }
    public function errorMessage()
    {
        return "statementException: {$this->getMessage()}";
    }
}
