<?php

require_once '../vendor/autoload.php';

use p810\MySQL\Connection;
use p810\MySQL\Model\Model;

$db = new Connection('root', 'secret', 'test');

class Users
extends Model
{}

$users = new Users($db);

$user = $users->find(1);

print $user->username . PHP_EOL;

$user->set('username', 'SomethingElse');

$updated = $users->find(1);

print $updated->username . PHP_EOL;