<?php

require_once '../vendor/autoload.php';

use p810\MySQL\Connection;

$db = new Connection('root', 'secret', 'test');

var_dump(
  $db->query->select("*", "users")
            ->orderAsc("user_id")
            ->execute()
);