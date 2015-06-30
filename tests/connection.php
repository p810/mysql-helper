<?php

require_once '../vendor/autoload.php';

use p810\MySQL\Connection;
use p810\MySQL\Exceptions\MySQLConnectionException;


/* Output will be an instance of p810\MySQL\Connection */

$connection = new Connection('root', 'secret', 'test');

var_dump($connection);


/* An exception will be thrown */

$connection = new Connection('doesnotexist', 'willthrowexception', 'haha');