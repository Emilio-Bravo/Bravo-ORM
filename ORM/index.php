<?php

require_once './vendor/autoload.php';

use Bravo\ORM\Query;

$query = new Query();
$query->attributes = ['question_title', 'correct_a'];
$query->table = "questions";
$result = $query->select()->all()->obj();
echo $result->correct_a;
