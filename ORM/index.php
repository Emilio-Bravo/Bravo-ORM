<?php

require_once './vendor/autoload.php';

use Bravo\ORM\Query;

$query = new Query;
$query->table = 'users';
$result = $query->select(['password'])->where('email')->like('emilio@gmail.com')->all()->obj();
echo $result->password;
