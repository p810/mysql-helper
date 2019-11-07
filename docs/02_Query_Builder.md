## Using the query builder
`p810\MySQL\ConnectionInterface` objects have factory methods for basic CRUD operations with MySQL: `insert()`, `select()`, `update()`, and `delete()`. `replace()` is also supported. Manual queries may be executed with `query()`.

Each method (except for `query()`) returns an instance of `p810\MySQL\Builder\BuilderInterface`. These objects can be fluently chained in any order to build your queries.

> :bulb: **Note:** These methods will return an instance of `p810\MySQL\Query`, which proxies your calls to an instance of `p810\MySQL\Builder\BuilderInterface`. You can directly instantiate the builder objects if you don't need the query functionality.

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
// select * from table ...
$connection->select()->from('table')
```

### `limit()`
To limit the number of results returned from the database, call `p810\MySQL\Builder\Select::limit()`:

```php
// select * from table ... limit 2
$connection->select()->from('table')->limit(2)
```

### `orderBy()`
To order the result set returned from the database by a certain column, call `p810\MySQL\Builder\Select::orderBy()` and specify the column name and direction (ascending or descending) that the results should be ordered (defaults to `desc`):

```php
// select * from table order by table_id desc
$connection->select()->from('table')->orderBy('table_id')
```

> :bulb: **Note:** This method may be called more than once to specify multiple `order by` clauses.

### Where clauses
`WHERE` clauses may be added to a query by the methods provided in `p810\MySQL\Builder\Grammar\Where`.

For example, adding a simple `WHERE` clause (*column* = *value*) can be done with `p810\MySQL\Builder\Grammar\Where::where()`:

```php
// select * from table where table_id = ? ...
$connection->select()->from('table')->where('table_id', 1)
```

> :bulb: **Note:** These methods automatically bind the values you provide for use in a prepared statement.

Each `where*()` method has an `orWhere*()` counterpart. For example, `whereLike()` and `orWhereLike()`:

```php
// select * from table where table_value like ? or table_value like ?
$connection->select()->from('table')->whereLike('table_value', 'foo')->orWhereLike('table_value', 'bar')
```

For more information on what all you can do with `p810\MySQL\Builder\Grammar\Where` see the [API docs](#).

### Join clauses
Data may be joined from other tables by the methods provided by `p810\MySQL\Builder\Grammar\Join`.

Each join type can be called by providing a table name for the clause, like:

```php
// select * from table inner join other_table ...
$connection->select()->from('table')->innerJoin('other_table')
// select * from table right join other_table ...
$connection->select()->from('table')->rightJoin('other_table')
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

> :bulb: **Note:** You must manually specify the table prefix for each column in the expression.

`p810\MySQL\Builder\Grammar\Join::on()` has an equivalent method that specifies `or` as the logical operator, `orOn()`:

```php
// select * from table inner join other_table on table.table_id = other_table.foo or table.table_id = other_table.bar
$connection->select()
           ->from('table')
           ->innerJoin('other_table')
           ->on('table.table_id', 'other_table.foo')
           ->orOn('table.table_id', 'other_table.bar')
```

To see the rest of this functionality, consult the [API docs](#).

## Insert
To insert new data, call `p810\MySQL\Connection::insert()`, optionally passing an associative array of column to value pairs containing the data you're inserting:

```php
// insert into ...
$connection->insert()
// insert into ... (foo, bar) values (?, ?)
$connection->insert([
    'foo' => 'hello',
    'bar' => 'world'
])
```

> :bulb: **Note:** Your values are automatically bound for a prepared statement.

### `into()`
Specify the table that this data should be placed in with `p810\MySQL\Builder\Insert::into()`:

```php
// insert into table ...
$connection->insert()->into('table')
```

### Priority
You can specify whether the query is of [high or low priority](https://stackoverflow.com/questions/3234972/what-are-the-advantages-of-update-low-priority-and-insert-delayed-into) by using `p810\MySQL\Builder\Insert::highPriority()` or `p810\MySQL\Builder\Insert::lowPriority()` respectively:

```php
// insert high_priority into table ...
$connection->insert()->highPriority()->into('table')
// insert low_priority into table ...
$connection->insert()->into('table')->lowPriority()
```

> :bulb: **Note:** `DELAYED` is not a supported option as it has been deprecated in MySQL 5.6.6 and removed in 5.7.

### `ignore()`
To ignore rows that may have invalid data and continue inserting valid rows (instead of erroring out), call `p810\MySQL\Builder\Insert::ignore()`:

```php
// insert ignore into table ...
$connection->insert()->into('table')->ignore()
```

### `columns()` and `values()`
Both `p810\MySQL\Builder\Insert::columns()` and `p810\MySQL\Builder\Insert::values()` take arrays to specify the query's columns and values:

```php
// insert into table (foo, bar) values (?, ?)
$connection->insert()
           ->into('table')
           ->columns(['foo', 'bar'])
           ->values(['hello', 'world'])
```

`p810\MySQL\Builder\Insert::values()` may take multiple arrays to insert multiple rows:

```php
// insert into table (foo, bar) values (?, ?), (?, ?)
$connection->insert()
           ->into('table')
           ->columns(['foo', 'bar'])
           ->values(
               ['hello', 'world'],
               ['hello', 'universe']
            )
```

### `onDuplicateKeyUpdate()`
An `on duplicate key update ...` clause may be appended with `p810\MySQL\Builder\Insert::onDuplicateKeyUpdate()` or the slightly shorter `p810\MySQL\Builder\Insert::updateDuplicate()`. These methods require a column and a value for the clause's expression. Multiple expressions may be appended by successive calls to the methods:

```php
// insert into table (...) on duplicate key update table_id = ?
$connection->insert()->into('table')->onDuplicateKeyUpdate('table_id', 'blah')
```

## Update
Updates are started with `p810\MySQL\Connection::update()`. You can optionally specify a table at instantiation via this method, or later on with `p810\MySQL\Builder\Update::table()`.

```php
// update table ...
$connection->update('table')
$connection->update()->table('table')
```

### `set()` and `setMany()`
`p810\MySQL\Builder\Grammar\Set::set()` takes a column (string) and value. This method can be called more than once if necessary.

```php
// update table set message = ?
$connection->update('table')->set('message', 'Testing, one two')
```

`p810\MySQL\Builder\Grammar\Set::setMany()` allows you to pass an associative array to set multiple columns at once:

```php
$connection->update('table')->setMany([
    'foo' => 'hello',
    'bar' => 'world'
])
```

### Where clauses
> :notebook: See the [above section on `WHERE` clauses](#where-clauses) or the [API docs](#) for more information.

## Delete
Deleting rows from a table is very straightforward. `p810\MySQL\Connection::delete()` may take a string specifying the table to delete rows from, or the table can be given to `p810\MySQL\Builder\Delete::from()`:

```php
// delete from table ...
$connection->delete('table')
$connection->delete()->from('table')
```

### Where clauses
> :notebook: See the [above section on `WHERE` clauses](#where-clauses) or the [API docs](#) for more information.

## Replace
`REPLACE` queries are identical to `INSERT` except that, if one of the given rows has a conflicting primary or unique key, it will delete that row before inserting its replacement. This query builder shares the same API as both `p810\MySQL\Builder\Insert` and `p810\MySQL\Builder\Update`.

To use an assignment list (update):

```php
// replace into table set message = ? where table_id = ?
$connection->replace('table')->set('message', 'Hello world')->where('table_id', 1)
```

> :bulb: **Note:** `p810\MySQL\Builder\Replace::set()` may be called more than one time to specify multiple assignments, or you can call `setMany()` with an associative array to achieve the same thing.

To use a value list (insert):

```php
// replace into table (message) values (?) where table_id = ?
$connection->replace('table')
           ->columns(['message'])
           ->values(['Hello world'])
           ->where('table_id', 1)
```

### Where clauses
> :notebook: See the [above section on `WHERE` clauses](#where-clauses) or the [API docs](#) for more information.

## Raw queries
`p810\MySQL\Connection::query()` takes a query string and an optional array of parameters to bind to the query for a prepared statement. If the query couldn't be prepared this method will return `null`. Otherwise, it will return `false` if query execution failed, or a `PDOStatement` object.

```php
$connection->query('select last_insert_id() from users limit 1')
```
