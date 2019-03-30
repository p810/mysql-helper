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