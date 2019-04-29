## Using the data mapper
The data mapper pattern is useful for decoupling your domain objects, sometimes referred to as models, from any database logic. With this pattern, your models define only their role in the flow of your applications, and mapper classes corresponding to those entities will read from and write to the database based on an entity's data. This results in cleaner domain objects that focus only on business logic, which makes your code more SOLID and simplifies things like changing your database or business logic later on.

## Entities
An entity, sometimes referred to as models or domain objects, are representations of your application's business logic. For example, in a simple blog you might define entities for your users, posts, and comments.

To utilize the data mapper, your entities should implement `p810\MySQL\Mapper\EntityInterface`, which asks you to define a method for returning new entities from certain data, and for representing the object as an array:

```php
class Person implements \p810\MySQL\Mapper\EntityInterface
{
    /**
     * @var string
     */
    public $name_first;

    /**
     * @var string
     */
    public $name_last;

    function __construct(string $name_first, string $name_last)
    {
        $this->name_first = $name_first;
        $this->name_last  = $name_last;
    }

    /**
     * {@inheritdoc}
     */
    public static function from(array $state): Person
    {
        [$first, $last] = explode(' ', $state['name']);
        
        return new self($first, $last);
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(): array
    {
        return (array) $this;
    }
}
```

## Mappers
A mapper is an object which translates the data belonging to an entity into something the database understands. Instances of `p810\MySQL\Mapper\MapperInterface` rely on a `p810\MySQL\ConnectionInterface` as an adapter to fluently build queries and interface with the data source. By default all instances of `p810\MySQL\Mapper\MapperInterface` must implement methods for CRUD operations with the database, and a generic query method.

Each CRUD method of a `MapperInterface` object allows you to manipulate the generated query by supplying a callback that takes a `p810\MySQL\Query` object and returns it, like so:

```php
$mapper->read(function (p810\MySQL\Query $query): p810\MySQL\Query {
    return $query->limit(5)->where('active', true);
});
```

> :bulb: **Note:** All read operations from the database will return instances of `p810\MySQL\Mapper\EntityInterface`, either individually or in an array (depending on the method).

Callbacks passed to the `create()`, `update()`, and `delete()` methods also take the `p810\MySQL\Mapper\EntityInterface` given to the outer function call as a second argument:

```php
$mapper->update($user, function ($query, $entity): p810\MySQL\Query {
    return $query->where('username', $entity->username);
});
```

In all cases, the `p810\MySQL\Query` object is what should be returned from the callback.

### `DefaultMapper`
`p810\MySQL\Mapper\DefaultMapper` is a base class from which other mappers may inherit useful default functionality. It's also well suited for data sources that use a key index to identify rows, providing methods such as `p810\MySQL\Mapper\DefaultMapper::findById()`.

Children of `p810\MySQL\Mapper\DefaultMapper` must define a `$table` and `$entity` string, specifying the table and `p810\MySQL\Mapper\EntityInterface` that the mapper corresponds to. To use functionality like `findById()`, the `$key` property may also be set:

```php
class PersonMapper extends p810\MySQL\Mapper\DefaultMapper
{
    /**
     * {@inheritdoc}
     */
    public $table = 'people';

    /**
     * {@inheritdoc}
     */
    protected $entity = Person::class;

    /**
     * {@inheritdoc}
     */
    public $key = 'person_id';
}
```

#### Getting the first result from a set
`p810\MySQL\Mapper\DefaultMapper::first()` will select data from the database and give you an entity from the first result in the set:

```php
$mapper->first(function (p810\MySQL\Query $query) {
    return $query->where('is_banned', true);
});
```

#### Querying by a row's ID
`p810\MySQL\Mapper\DefaultMapper::findById()`, `updateById()`, and `deleteById()` all may take an integer to simplify common where clauses for these operations. `findById()` and `deleteById()` both *only* take an integer, while `updateById()` also takes an instance of `p810\MySQL\Mapper\EntityInterface` to source data for the update:

```php
//=> p810\MySQL\EntityInterface(1)
$entity = $mapper->findById(1);
$entity->foo = "I am being updated";

//=> bool(true)
$mapper->updateById(1, $entity);

//=> bool(true)
$mapper->deleteById(1);
```

## Live entities
If you're worried about the verbosity of having to manage multiple objects for your models, or favor a more Active Record inspired approach, `p810\MySQL\Mapper\RowMapper` should make things a bit easier for you. This mapper extends `p810\MySQL\Mapper\DefaultMapper` in such a way that it returns instances of `p810\MySQL\Mapper\Row`, which is an `EntityInterface` that wraps around another `EntityInterface` *and* the `MapperInterface` that represents it.

With a `p810\MySQL\Mapper\Row`, you can update and/or delete the database row associated with the object by calling `save()` or `delete()` respectively. Additional functionality for relationship modeling is planned for a future release.

> :bulb: **Note:** `RowMapper`s are defined in the same way as `DefaultMapper`s shown above.

```php
//=> p810\MySQL\Mapper\Row(1)
$user = $mapper->first(function ($query) {
    return $query->where('username', 'Bob');
});

$user->username = 'Bobbert';

//=> bool(true)
$user->save();
```