<?php

require_once '../vendor/autoload.php';

use p810\MySQL\Connection;
use p810\MySQL\Query;

$connection = new Connection('root', 'secret', 'test');

$query = new Query($connection);

var_dump(
  $query->select("*", "users")
        ->whereEquals("username", "Bob")
        ->execute()
        ->fetchAll(PDO::FETCH_ASSOC)
);