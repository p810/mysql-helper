### 3.2.0
- Removed `p810\MySQL\Builder\Grammar\Where::and()` and `Where::or()` to simplify the trait's logic, since these don't provide much benefit over just chaining the next method, and may arguably hurt readability
- Introduced `p810\MySQL\Builder\BuilderInterface` and replaced `p810\MySQL\Builder\Builder` with `p810\MySQL\Builder\AbstractBuilder`
    * `AbstractBuilder::$input` is now protected since `BuilderInterface::getParameters()` is now available, making that functionality part of the contract
    * The constant `AbstractBuilder::COMMAND` was replaced with `BuilderInterface::getCommand()` which makes that functionality contractual as well
    * Changed `AbstractBuilder::build()` to use a loop instead of a call to `array_reduce()` for the sake of micro-optimizations
- Various formatting and code style changes

Finally squashed all of those errors and notices from Psalm! There are some suppressed that I want to come back and fix later when things like union types are available, though.

### 3.1.2
- Updated `Query` to pass null instead of false to processor callbacks when a statement fails to be built in `ConnectionInterface::query()`
    * This allows processors to include a nullable type hint for `PDOStatement` if they plan to handle failure cases
- Added new grammar traits:
    * `Priority` which moves `highPriority()` and `lowPriority()` from the Insert builder
    * `Ignore` which moves `ignore()` from the Insert builder
    * `Limit` which moves `limit()` from the Select builder
    * `Table` which moves `from()` from the Select and Delete builders, and `into()` from the Insert and Replace builders
    * `Set` which moves `set()` from the Update and Replace builders
        - `set()` now only takes a column and value; to specify multiple columns with an array, use `setMany()`
- Updated various builders to utilize new grammar traits:
    * Select, Delete, Insert, and Replace now use `Table` to specify the query's source table
    * Select and Delete can now use functionality from `Priority`
    * Delete may now use `Ignore`, `OrderBy`, and `Limit` functionality
- Removed a condition that would raise an exception if the priority value in a Replace query was invalid
- Changed `p810\MySQL\Processor\Pdo` to `PdoProcessor` for consistency with `AbstractProcessor` and to better communicate the purpose of the class
- Updated `p810\MySQL\Mapper\DefaultMapper::lastInsertId()` to use the query builder instead of a raw query
- Formatting and code style changes:
    * Removes unused class and function imports
    * Updates multiple instances of object instantiation that lacked parentheses after the class name
    * Updates multiple docblocks to:
        - Ensure lines are not broken before hitting the 120th column, per PSR rules
        - Ensure class names, methods, etc. are wrapped with backticks
        - Ensure annotations are consistently spaced; for example, some param lists were formatted like tables while others weren't
- Added `Builder::__toString()` which is an alias for `Builder::build()` and allows for passing/representing builder objects as a string

I think this package is about to the point where it's mature enough that I'll finally make myself start following semver. Of course, I've felt that way before and turned around and overhauled much of the internals a few months later, so who knows. At this point I still enjoy the flexibility I have with versioning because I can move quickly and break things without risking harming anyone's projects. Eventually I'll muster up the courage to share this on r/php et. al places, but for now I'll tinker away at it in solitude.

### 3.1.1
- Removed `league/climate` dependency
- Added `league/climate` as a *dev* dependency

Woops, should have thought about that before I pushed the last tag.

### 3.1.0
- Changes to how you interact with query processor objects
    * `p810\MySQL\ConnectionInterface::setCommandHandler()` has been removed
    * `p810\MySQL\ConnectionInterface::getProcessor()` has been added
- `Processor` is now its own namespace
    * `p810\MySQL\Processor` has become `p810\MySQL\Processor\AbstractProcessor`
    * `p810\MySQL\Processor\ProcessorInterface` has been added so that the `ConnectionInterface` isn't dependent on an implementation
    * `p810\MySQL\PdoProcessor` is now `p810\MySQL\Processor\Pdo`
- `Builder` classes now have a `COMMAND` constant which will be used to identify them to the processor; previously it used the Reflection API which I wasn't fond of
- Removed `p810\MySQL\Builder\Select::prefixColumns()` because it's easier just to write the prefix, and I didn't test it at first so I failed to realize that only one column could claim a table name, which isn't very helpful (and I didn't feel it was worth adding the complexity to support nested arrays, because who would ever want to write the query like that)<sup>*</sup>
- Fixed an oversight in `p810\MySQL\Mapper\Row::toArray()` where columns (properties) with null values would not be included
- Moved `p810\MySQL\ConnectionInterface::getDsn()` to `p810\MySQL\makePdoDsn()`
- Updated source to respect PSR-2 guideline on line length
    * This only applies to code lines, for the most part I left documentation untouched
    * Strings are left in tact to allow searching / preveng ugliness
    * In general I tried to keep lines under 80 chars, however some go up to 120 (the hard limit) for readability and presentation reasons -- which is *ok* IIRC
- Fixed some problems with the migration script...
    * Suppressed a PHP notice that's raised when `get_cli_arg()` is called and no arguments were passed
    * Added a condition to check for the success of `json_decode()`
    * Removed `--table` since it's defined in the configuration file
- ... and then rewrote the migration script
    * Integrated League's CLImate package
    * Added a usage message
    * Added a shorthand form for the `--file` argument (`-f`, obviously)
    * Reworked parts of the script to be compliant with PSR-2

Technically (according to semantic versioning) this should be a major release, but I don't feel like jumping up a major version number yet, and no one uses this package so it doesn't matter if I make backwards incompatible changes. One day I'll advertise this package somewhere, but for now I'll enjoy my autonomy.

When I pushed 3.0.0 back in April, I felt uneasy about the way that the processor logic was coupled to the `Connection` class, though I wasn't entirely sure why or what to do about it. I was too busy fixing other parts of the codebase that I felt deserved more attention at the time, and my concern fell to the background for a bit. Well, now I've addressed it, and I feel a bit better about this little project overall. It was weird to have a method on the `ConnectionInterface` to do what was obviously the responsibility of a processor. Also, I've recognized that having a dependency on an abstract implementation is still a violation of the dependency inversion principle. So I made it so that `ConnectionInterface` expects a `ProcessorInterface` instead of the former, abstract `Processor` class. Learning!

<details>
<summary><sup>* Here's an example of what that would look like:</sup></summary>

```php
$query->select([
    ['user' => 'username'],
    ['user' => 'password']
]) // select user.username, user.password (...)
```

I could invert the keys and values, but that would not be as intuitive and presents the problem of collisions with column names, which, though less likely for the majority of cases, is still a problem. It's easiest just to write it out.
</details>

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
