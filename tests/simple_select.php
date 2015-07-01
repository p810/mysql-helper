<?php

require_once '../vendor/autoload.php';

use p810\MySQL\Connection;

$db = new Connection('root', 'secret', 'test');

var_dump(
  $db->query->select("*", "users")
            ->orderDesc("user_id")
            ->limit(2)
            ->execute()
);