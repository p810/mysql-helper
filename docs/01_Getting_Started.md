## Connecting to the database
To connect to the database, create a new instance of `p810\MySQL\Connection`:

```php
$connection = new p810\MySQL\Connection('username', 'password', 'database');
```

The default hostname is `127.0.0.1`. This can be overridden by passing a string as the fourth argument.

By default, the `Connection` will tell PDO that it should raise exceptions when an error occurs. To disable this via the constructor, pass `false` as the fifth argument.

If you want to specify any additional parameters for the DSN string that's passed into PDO's constructor, you can provide an associative array as the sixth argument.

Any additional arguments you want to pass into `PDO::__construct()` may be provided via an array as the seventh argument.

```php
$connection = new p810\MySQL\Connection(
    'username',
    'password',
    'database',
    '127.0.0.1',
    true,
    [ /* DSN params... */ ],
    [ /* constructor args... */ ]
);
```

### Get the `PDO` instance
To get the instance of `PDO` that the `Connection` uses, call `Connection::getPdo()`:

```php
$pdo = $connection->getPdo();
```

## Connection options
### Setting PDO attributes
To change PDO attributes after construction of the `Connection` object, call `Connection::setAttribute()`:

```php
$connection->setAttribute(PDO::ATTR_CASE, PDO::CASE_LOWER);
```

### Toggling the error mode
To change the `PDO::ATTR_ERRMODE`, call `Connection::shouldThrowExceptions()` and pass a boolean indicating whether to raise exceptions or silently fail (true or false, respectively; defaults to true):

```php
// sets PDO::ATTR_ERRMODE to PDO::ERRMODE_SILENT:
$connection->shouldThrowExceptions(false);
```

### Toggling auto-commit
To toggle the `PDO::ATTR_AUTOCOMMIT` attribute, which tells PDO whether it should automatically commit query results, call `Connection::shouldAutoCommit()` and pass a boolean indicating whether to auto-commit or not (true or false, respectively; defaults to true):

```php
// sets PDO::ATTR_AUTOCOMMIT to true:
$connection->shouldAutoCommit();
```

## Transactions
To begin a [transaction](https://www.php.net/manual/en/pdo.transactions.php), call `Connection::transact()` or `Connection::beginTransaction()`, which will return a boolean indicating whether the transaction was successfully started, or throw a `PDOException` if something went wrong. If a transaction is already active for the connection it will return true.

You can also test whether a transaction is active on the connection by calling `Connection::inTransaction()`, which will return a boolean.

To commit the result of a query during a transaction, call `Connection::commit()`. To rollback (undo) the result call `Connection::rollback()`. Either of these methods will return a boolean indicating success or failure, or throw a `PDOException` if something went wrong.

```php
$connection->beginTransaction();

$successful = $connection->insert($input)->into($table)->execute();

if ($successful) {
    $connection->commit();
} else {
    $connection->rollback();
}
```
