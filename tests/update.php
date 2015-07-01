<?php

require_once '../vendor/autoload.php';

use p810\MySQL\Connection;
use p810\MySQL\Query;

$db = new Connection('root', 'secret', 'test');

var_dump(
  $db->query->update("users", ['username' => 'Tom'])
            ->where('user_id', 1)
            ->execute()
);