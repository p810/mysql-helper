## Connection
To connect to the database, create a new instance of `p810\MySQL\Connection`. Then you should pass this object into
`Query::setConnection()` to be able to execute queries with the query builder.

**Note: The default host is `localhost`. You do not need to specify the fourth argument unless it is different from this.**

```php
use p810\MySQL\Connection;
use p810\MySQL\Query;

$connection = new Connection('root', 'root', 'database', 'localhost');
Query::setConnection($connection);
```

### Transactions and autocommit
You may disable autocommit by calling `Connection::autocommit(false)`, or enable it by passing `true` (or no value).

Transactions may be started with `Connection::beginTransaction()` or `Connection::transact()`. The same applies to `Query` objects. The same methods as PDO are used to commit or roll-back changes, `commit()` and `rollBack()` respectively.

```php
$connection->transact();

Query::setConnection($connection);
Query::delete()
  ->from('users')
  ->where(/* ... */)
  ->execute();

// uh oh, something went wrong! better just...
$connection->rollBack();
```