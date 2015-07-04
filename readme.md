This is my experimental query builder and modeling library developed for use with PHP and MySQL. The overall goal of this project is to create an easily expressed API for interacting with MySQL in projects I develop down the road. I'm also doing it to gain more knowledge of SQL and best design practices in OOP.

Feel free to fork and contribute or to submit issues and feature requests!


# Installation

Composer is recommended to install this library. Your usual `composer install` in the project directory will create an autoloader. You may also take the `src/` directory and autoload the files yourself. Currently there are no external dependencies that are loaded through Packagist.


# Usage

To get started you must import `p810\MySQL\Connection` and create an instance. This class will provide magic access to the underlying functionality in `p810\MySQL\QueryFactory`, however note that it can also be used independently.

```php
<?php

use p810\MySQL\Connection;

$db = new Connection('username', 'password', 'database');

?>
```


## Building queries

Queries can be built by chaining the appropriate methods to your statement (eg `select`). Each query will be stored until you choose to execute it, by calling the `execute()` method. If your query fails for whatever reason, `false` is returned.

The following are examples of queries built with the library.


### Selecting data

```php
<?php

$db->select('username', 'users') // Grab only usernames
   ->where('user_id', '>', 1)    // The user_id must be greater than one
   ->orderDesc('user_id')        // Fetch results in descending order
   ->limit(50);                  // Give no more than fifty results

?>
```


### Inserting data

```php
<?php

// Insert a new user. The key is your table's column.

$db->insert('users', array(
  'username' => 'Payton'
));

?>
```


### Updating data

```php
<?php

// Ban that wild cowboy who's too cool for the rules. B(

$db->update('users', ['banned' => true])
   ->where('username', 'RuleBreaker');

?>
```


## Models

Models are a way to encapsulate database tables with classes. This helps to describe database logic in a way that I feel is easier to interpret and work with.

My strategy for modeling is still very early in development and not yet mature enough to where I'm proud of it. Any feedback is appreciated!


### Creating a model

To create a model, declare a class as an extension of `p810\Model\Model`. Declare two public properties, the table name and primary key.

**Note: The first argument passed to your model must be an instance of `p810\MySQL\Connection`.**

```php
<?php

use p810\Model\Model;

class Users
{
  public $table = 'users';
  public $pk = 'user_id';
}

$users = new Users($db);

?>
```


### Finding a row

Find a row by its ID by calling the `find()` method.

```php
<?php

$some_person = $users->find(1);

?>
```

This will return an instance of `p810\Model\Row`.


### Data access and updating

You can access data by requesting a column name like it were a property of the class.

```php
<?php

print $some_person->username;

?>
```

You can update data by calling the `set()` method on the row, which will automatically commit it to the database for you.

```php
<?php

$some_person->set('username', 'NewNameForThisGuy');

?>
```