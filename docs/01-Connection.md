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