<?php

namespace p810\MySQL;

use function implode;
use function is_array;

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
 * @param string $database MySQL database name
 * @param array $arguments PDO arguments
 * @return string
 */
function makePdoDsn(string $host, string $database, array $arguments = []): string
{
    $dsn = "mysql:host=$host;dbname=$database";

    if ($arguments) {
        foreach ($arguments as $argument => $value) {
            $dsn .= ";$argument=$value";
        }
    }

    return $dsn;
}
