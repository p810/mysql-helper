<?php

namespace p810\MySQL;

use function implode;
use function is_array;
use function array_key_exists;
use function array_map;

/**
 * A map of words that are reserved in PHP and may be used in SQL queries to their string representations
 * 
 * @var array<mixed,string>
 */
const KEYWORD_AS_STRING = [
    true  => 'true',
    false => 'false',
    null  => 'null'
];

/**
 * Returns the given value surrounded by parentheses
 * 
 * @param string|array $value The value to surround with parentheses; if this is an array, it will be turned into a
 *                            comma delimited string
 * @return string
 */
function parentheses($value): string
{
    if (is_array($value)) {
        $value = commas($value);
    }
    
    return '(' . $value . ')';
}

/**
 * Returns a comma delimited string from an array of values
 * 
 * @param array $list A list of values to transform
 * @return string
 */
function commas(array $list): string
{
    return implode(', ', array_map("p810\MySQL\keywordToString", $list));
}

/**
 * Returns a space delimited string from an array of values
 * 
 * @param array $list A list of values to transform
 * @return string
 */
function spaces(array $list): string
{
    return implode(' ', array_map("p810\MySQL\keywordToString", $list));
}

/**
 * Returns a DSN string to be used with PDO's constructor
 * 
 * @param string $host MySQL host name e.g. localhost
 * @param null|string $database MySQL database name, or null if not needed
 * @param array<string|int,mixed> $arguments An optional, associative array of DSN parameters
 * @return string
 */
function makePdoDsn(string $host, ?string $database = null, array $arguments = []): string
{
    $dsn = "mysql:host=$host";

    if ($database && ! array_key_exists('dbname', $arguments)) {
        $dsn .= ";dbname=$database";
    }

    if ($arguments) {
        foreach ($arguments as $argument => $value) {
            $dsn .= ";$argument=$value";
        }
    }

    return $dsn;
}

/**
 * Returns a string representation of the given reserved word in PHP, for use in a query
 * 
 * @param bool|null $value A boolean or null
 * @param bool $nullIsUnknown Whether to return `UNKNOWN` for null, i.e. in an `IS` comparison
 * @return string
 */
function keywordToString($value, bool $nullIsUnknown = false): string
{
    if (! is_bool($value) && $value !== null) {
        return $value;
    } elseif ($value === null && $nullIsUnknown) {
        return 'unknown';
    }

    return KEYWORD_AS_STRING[$value];
}

/**
 * Returns a string representing a call to `GREATEST()`
 * 
 * @param array $values A list of values
 * @return string
 */
function greatest(array $values): string
{
    return 'greatest(' . commas($values) . ')';
}

/**
 * Returns a string representing a call to `LEAST()`
 * 
 * @param array $values A list of values
 * @return string
 */
function least(array $values): string
{
    return 'least(' . commas($values) . ')';
}

/**
 * Returns a string representing a call to `COALESCE()`
 * 
 * @param array $values A list of values
 * @return string 
 */
function coalesce(array $values): string
{
    return 'coalesce(' . commas($values) . ')';
}
