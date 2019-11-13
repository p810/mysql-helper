<?php

namespace p810\MySQL;

use function implode;
use function is_array;
use function array_key_exists;

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
    return implode(', ', $list);
}

/**
 * Returns a space delimited string from an array of values
 * 
 * @param array $list A list of values to transform
 * @return string
 */
function spaces(array $list): string
{
    return implode(' ', $list);
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
