<?php

require_once '../vendor/autoload.php';

use p810\MySQL\Connection;
use p810\MySQL\Query;

$connection = new Connection('root', 'secret', 'test');

$query = new Query($connection);

var_dump(
  $query->update("users", ['username' => 'Tom'])
        ->where('user_id', 1)
        ->execute()
);