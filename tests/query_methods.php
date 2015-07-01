<?php

require_once '../vendor/autoload.php';

use p810\MySQL\Connection;

$db = new Connection('root', 'secret', 'test');


/* This will catch a BadMethodCallException */

try {
  $db->does_not_exist();
} catch(BadMethodCallException $e) {
  print 'Caught BadMethodCallException' . PHP_EOL;
}


/* This will allow us to select data without having to chain Connection::$query */

var_dump(
  $db->select("username", "users")
   ->where("username", "Tom")
   ->execute()
);