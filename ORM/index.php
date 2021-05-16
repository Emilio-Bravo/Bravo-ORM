<?php

require_once './vendor/autoload.php';

use Bravo\ORM\Model;

$result = Model::find(['name' => 'Emilio'])->obj();
echo $result->email;