`p810\MySQL\Connection` has factory methods for basic CRUD operations with MySQL: `insert()`, `select()`, `update()`, and `delete()`. `replace()` is also supported. Manual queries may be executed with `raw()`.

Each method corresponds to a subclass of `p810\MySQL\Builder\Builder`. These objects can be fluently chained in any order to build your queries.

> **Note:** These methods will return an instance of `p810\MySQL\Query`, which proxies your calls to an instance of `p810\MySQL\Builder\Builder`. You can directly instantiate the builder objects if you don't need the query functionality.

## Select
To start a `SELECT` query, run `p810\MySQL\Connection::select()`. You can pass a string or array to specify columns; no value defaults to `*`. If the given value is an array, it can either be a list (numeric array) or associative to specify table names before the columns:

```php
$connection->select() // select * ...
$connection->select('foo') // select foo ...
$connection->select(['foo', 'bar']) // select foo, bar ...
$connection->select(['a' => 'foo', 'b' => 'bar']) // select a.foo, b.bar ...
```

You can also specify columns for your query in the same fashion by calling `p810\MySQL\Builder\Select::columns()`.

### `from()`
To specify a table for the query, call `p810\MySQL\Builder\Select::from()`:

```php
$connection->select()->from('table') // select * from table ...
```

### `limit()`
To limit the number of results returned from the database, call `p810\MySQL\Builder\Select::limit()`:

```php
$connection->select()->from('table')->limit(2) // select * from table ... limit 2
```

### `orderBy()`
To order the result set returned from the database by a certain column, call `p810\MySQL\Builder\Select::orderBy()` and specify the column name and direction (ascending or descending) that the results should be ordered (defaults to `desc`):

```php
$connection->select()->from('table')->orderBy('table_id') // select * from table order by table_id desc
```

> **Note:** This method may be called more than once to specify multiple `order by` clauses.

### Where clauses
`WHERE` clauses may be added to a query by the methods provided in `p810\MySQL\Builder\Grammar\Where`.

For example, adding a simple `WHERE` clause (*column* = *value*) can be done with `p810\MySQL\Builder\Grammar\Where::where()`:

```php
$connection->select()->from('table')->where('table_id', 1) // select * from table where table_id = ? ...
```

> **Note:** These methods automatically bind the values you provide for use in a prepared statement.

Each `where*()` method has an `orWhere*()` counterpart. For example, `whereLike()` and `orWhereLike()`:

```php
// select * from table where table_value like ? or table_value like ?
$query = $connection->select()->from('table')->whereLike('table_value', 'foo')->orWhereLike('table_value', 'bar')
```

You can also chain methods with `and()` and `or()`:

```php
$connection->select()->from('table')->where('foo', 'bar')->or()->where('bar', 'foo') // select * from table where foo = ? or bar = ?
```

For more information on what all you can do with `p810\MySQL\Builder\Grammar\Where` see the [API docs](#).

### Join clauses
Data may be joined from other tables by the methods provided by `p810\MySQL\Builder\Grammar\Join`.

Each join type can be called by providing a table name for the clause, like:

```php
$connection->select()->from('table')->innerJoin('other_table') // select * from table inner join other_table ...

$connection->select()->from('table')->rightJoin('other_table') // select * from table right join other_table ...
```

You can specify that the tables should be joined by the same column with `using()`:

```php
// select * from table inner join other_table using(table_id)
$connection->select()->from('table')->innerJoin('other_table')->using('table_id')
```

Or you can specify one or more expressions with `on()`:

```php
// select * from table inner join other_table on table.table_id = other_table.foo
$connection->select()->from('table')->innerJoin('other_table')->on('table.table_id', 'other_table.foo')
```

> **Note:** You must manually specify the table prefix for each column in the expression.

Comparable to `p810\MySQL\Builder\Grammar\Where`, `p810\MySQL\Builder\Grammar\Join::on()` has an equivalent method that specifies `or` as the logical operator, `orOn()`:

```php
// select * from table inner join other_table on table.table_id = other_table.foo or table.table_id = other_table.bar
$connection->select()
           ->from('table')
           ->innerJoin('other_table')
           ->on('table.table_id', 'other_table.foo')
           ->orOn('table.table_id', 'other_table.bar')
```

To see the rest of this functionality, consult the [API docs](#).