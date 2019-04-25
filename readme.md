# mysql-helper
> A fluent query builder and lightweight data mapper for MySQL

## Installation
This package is available through Packagist.

```
$ composer require p810/mysql-helper --no-dev
```

## Getting started
### Using the query builder
Connect to MySQL with a new instance of `p810\MySQL\Connection`:

```php
$connection = new p810\MySQL\Connection('username', 'password', 'database');
```

> **Note:** For more connection options, see [the documentation](#).

Then use the builder factory methods to fluently build your SQL queries with `p810\MySQL\Builder\Builder`s:

```php
$query = $connection->select()->from('users')->where('username', 'Bob');
$result = $query->execute();

if ($result) {
    foreach ($result as $row) {
        echo $row['username'] . '<br>';
    }
}
```

The available factory methods are `select()`, `insert()`, `update()`, `delete()`, and `replace()`. To run a query and get its `PDOStatement` object (rather than process the results), you can run `p810\MySQL\Connection::raw()`:

```php
$statement = $connection->raw('select last_insert_id() from users limit 1');
```

`raw()` also supports binding input for prepared statements. Just pass an array after the query.

### Entities and data mappers
You can make models of your domain logic by implementing `p810\MySQL\Mapper\EntityInterface`. These models can be mapped to MySQL via implementations of `p810\MySQL\Mapper\MapperInterface`. Most mappers should extend `p810\MySQL\Mapper\DefaultMapper`, as it handles most operations out of the box:

```php
class User implements \p810\MySQL\Mapper\EntityInterface
{
    /**
     * @var string
     */
    public $username;

    /**
     * @var string
     */
    public $password;

    /**
     * {@inheritdoc}
     */
    public static function from(array $state): EntityInterface
    {
        return new self($state['username'], $state['password']);
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(): array
    {
        return (array) $this;
    }

    /**
     * @param string $username
     * @param string $password
     */
    function __construct(string $username, string $password)
    {
        $this->username = $username;
        $this->password = $password;
    }
}
```

```php
class UserMapper extends \p810\MySQL\Mapper\DefaultMapper
{
    /**
     * {@inheritdoc}
     */
    public $table = 'users';

    /**
     * {@inheritdoc}
     */
    public $key = 'user_id';

    /**
     * {@inheritdoc}
     */
    protected $entity = User::class;
}
```

When running a query via a mapper, you have the option to manipulate the `p810\MySQL\Query` that it generates by passing a callback to the method call:

```php
$users = $mapper->read(function (\p810\MySQL\Query $query) use ($input) {
    return $query->where('username', $input['username'])
                 ->innerJoin('profiles')
                 ->using('user_id');
});
```

> **Note:** It's recommended that you specify your query's constraints in methods belonging to your mapper. Be careful not to run a CRUD method via the mapper without manipulating the query unless you have an absolute use case, otherwise you will end up with potentially large result sets or dangerous side effects. For example, `DefaultMapper::delete()` without any customization will delete *every* row in the table represented by your mapper.

If you want to run a generic query against the `p810\MySQL\Connection`, you can pass it through `MapperInterface::query()` which has the same signature as `Connection::raw()`.

### Live entities
For users who are more accustomed to Active Record inspired models, your mapper may extend `p810\MySQL\Mapper\RowMapper` which provides all the functionality of `p810\MySQL\Mapper\DefaultMapper`, but returns instances of `p810\MySQL\Mapper\Row` which wrap your entity and mapper for a less verbose experience:

```php
$user->password = 'some_new_password1@%';

// This is equivalent to calling DefaultMapper::updateById(1, $user)
$user->save();
```

For more information on what's possible with the query builder and data mapper, check out [the documentation](#).

## Development
### Unit testing
```
$ ./bin/migrate
$ ./vendor/bin/phpunit ./test/
```

A file named `.db.env` is loaded when PHPUnit is run, to get database connection options. An example of this file's contents can be found in `.db.env.example`. To change the location of this file, modify the environment variable in `phpunit.xml`.

The migration script will create a small table for the unit tests to read from and write to. You may specify a different table when running the script by passing `--table` and updating the corresponding key in `.db.env`. If you're using a file other than `.db.env` be sure to pass the `--file` flag when running the migration.

### Code quality
```
$ ./vendor/bin/psalm --show-info=false
```

## License
This package is released under the [MIT License](https://github.com/p810/mysql-helper/blob/master/LICENSE).
