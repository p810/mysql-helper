### 3.0.0
- Rewrites the way that queries are compiled
    * Builder objects follow SRP better: `p810\MySQL\Processor` replaces `p810\MySQL\Builder\Builder::handleResults()`
    * Each component now has a `compile*()` method that's dynamically mapped to the component name in `Builder::$components`, which is iterated over at compile time
- API for where clauses was rewritten
    * Code is much more sane, opting for multiple methods (e.g. `whereNotEquals()`, `whereLessThan()`, etc.) rather than variadic arguments
    * Nested where clauses are now possible, along with a number of other predicates, e.g. "where not in"
- API for join clauses introduced!
- Changes `p810\MySQL\Connection` and `p810\MySQL\Query` so that `Connection` is the factory: No more `Query::setConnection()`!
- Adds new functionality to `p810\MySQL\ConnectionInterface` objects
    * Allows for different database connectors (e.g. PDO, MySQLi) per implementation
    * Introduces `p810\MySQL\Processor` objects for handling query results
    * Adds `query()` and `prepare()` methods for direct access to the connector/generic queries (without builder)
- Replaces `Model` namespace with data mapper implementation
    * Specifies an interface for entities (models) and their mappers
    * Provides a default implementation that's well suited for most tables
    * Provides a special mapper that yields instances of `p810\MySQL\Mapper\Row`, which wraps an entity and its mapper for an approach more reminiscient of Active Record and the old `Model` classes
- Removes the `Exception` namespace
- Updates `functions.php`
    * Removes query functions e.g. `select()` as the new API is incompatible with these
    * Adds helper functions for common array/string joining e.g. `p810\MySQL\commas()`
- Covers (almost, if not) the entire codebase with test cases
    * Adds a migration script for unit tests in `./bin/migrate`
    * Adds PHPUnit configuration in `phpunit.xml` and `.db.env`
- Adds configuration for Vimeo's Psalm, a code quality analysis tool
- Removes unused Composer dependencies
- Adds full documentation in `./docs/`

This is a pretty big release to me. In some two hundred commits, I feel I've ironed out a lot of the kinks and oddities that existed in the first couple versions of this project, and I think I have a tool that I will truly enjoy using now. It's also paved the way for future updates that should prove to be fun and useful I think, and I'm excited to work on things like relational mapping between entities, command line tools, and new builder syntax as this grows. It's hard to believe I've been working on this package since 2015! I feel like 3.0 highlights some of my growth as a programmer, because compared to how the first two versions looked and functioned, this code feels much more SOLID and user friendly, living up to the original ethos of the library. Still though, I'm sure there's more room for improvement, and I intend to keep at it.

### 2.1.2
- Adds the MIT license to the repository

### 2.1.1
- Code clean up!
    * Import functions that belong to the global namespace
    * Each open brace for functions and classes belongs on a new line
    * Import all the things, no directory separators before class names
    * Mark methods that raise exceptions with `@throws` annotation
- Adds "Getting started" section to readme.md

There was also a slight change to `p810\MySQL\Query\Where` (`where()`/`or()`) and `p810\MySQL\Query\Values` (`setValues()`): These methods of these traits previously raised an `UnexpectedValueException` when one of their variadic arguments was invalid. I've replaced this for `InvalidArgumentException` which obviously makes more sense, out of the SPL exceptions. Technically this is a change that could be considered backwards incompatible, and may warrant more than just a patch version, but since no one uses this library, I'm taking some liberties with it.

### 2.1.0
- Added methods for PDO's transactions functionality to `p810\MySQL\Connection` and `p810\MySQL\Query`
    * Added `p810\MySQL\Exception\TransactionCouldNotBeginException`
    * Updated the documentation under `docs/01-Connection.md` with examples
- Added `p810\MySQL\Test\Credentials` to load database credentials for the unit tests via an environment variable
- Some code clean up:
    * Some docblock typehints were updated for better IDE autocompletion, e.g. `PDO` -> `\PDO`
    * Replaced `@expectedException` annotation with `PHPUnit\Framework\TestCase::expectException()` in `ConnectionTest.php`
    * `use` some classes e.g. `PDO` for style consistency (to be changed elsewhere in the next patch)

This is likely going to be the last (and only) minor version in the 2.x release. I'm planning on doing a major overhaul of the code that may even become a rewrite; it's hard to believe this package is already over three years old. I've learned a good bit in the past few years so I want 3.0 to reflect where I'm currently at, personally. I'm leaning heavily toward implementing a flavor of the data mapper pattern, as I've grown to favor it over active record, which the current and previous version were influenced by.