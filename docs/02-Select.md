## Select
```php
use p810\MySQL\Query;

// SELECT * FROM users
Query::select()
    ->from('users')
    ->execute();

// SELECT name FROM users WHERE user_id = 1
Query::select('name')
    ->from('users')
    ->where('user_id', 1)
    ->execute();

// SELECT * FROM users WHERE user_id != 1 AND name = 'Payton'
Query::select()
    ->from('users')
    ->where('user_id', '!=', 1)
    ->and('name', 'Payton')
    ->execute();

// SELECT * FROM users WHERE name = 'Bob' OR name = 'Laura'
Query::select()
    ->from('users')
    ->where('name', 'Bob')
    ->or('name', 'Laura')
    ->execute();

// SELECT user_id, name FROM users WHERE user_id = 1 OR user_id = 2 AND name != 'Payton'
Query::select(['user_id', 'name'])
    ->from('users')
    ->where([
        'user_id' => 1,
        'user_id' => ['=', 2, 'OR'],
        'name'    => ['!=', 'Payton']
    ])
    ->execute();
```