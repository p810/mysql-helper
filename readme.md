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

## Documentation
Documentation is available under the `docs/` subdirectory.