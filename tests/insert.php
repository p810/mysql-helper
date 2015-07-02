<?php

require_once '../vendor/autoload.php';

use p810\MySQL\Connection;

$db = new Connection('root', 'secret', 'test');

var_dump(
  $db->insert('users', [
    'username' => 'Payton'
  ])->execute()
);