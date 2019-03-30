# mysql-helper
> A MySQL DBAL with a human friendly API.

This package is my attempt at writing a DBAL that will make database logic easier to read and write in an application.
Currently it can write queries for simple CRUD operations -- I'm working on functionality for the ORM side of things,
and plan to expand upon the query builder to support things beyond basic query syntax (e.g. `JOIN` clauses).

## Installation
This package is available through Packagist.

```
composer require p810/mysql-helper --no-dev
```

### For development purposes
```
git clone https://github.com/p810/mysql-helper.git
cd mysql-helper/
composer install
```

A file named `.db.env` is loaded when PHPUnit is run. An example of this file's contents can be found in `.db.env.example`. You may also have PHPUnit load a different file by modifying `phpunit.xml`.

## Getting started
To get started using the query builder, first inject a connection into the query builder factory:

```php
use p810\MySQL\Query;
use p810\MySQL\Connection;

$db = new Connection('username', 'password', 'database');

Query::setConnection($db);
```

Then you can use `p810\MySQL\Query` to create builder objects, which return what you'd expect from PDO when executed:

```php
$query = Query::select('username')->from('users');
$query->where('user_id', 37);

var_dump($query->execute()); //=> array(1) { [0] => array(1) { ... } }
```

You can also represent your schema by PHP objects with `p810\MySQL\Model`:

```php
use p810\MySQL\Model;

class Users extends Model
{
    /** @var string */
    protected $table = 'users';

    /** @var string */
    protected $primaryKey = 'user_id';
}
```

API documentation and further code examples can be found in the `docs/` subdirectory.
