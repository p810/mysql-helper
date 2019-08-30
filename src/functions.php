<?php

namespace p810\MySQL;

use function implode;
use function is_array;

/**
 * Returns the given value surrounded by parentheses
 * 
 * @param string|array $value The value to surround with parentheses; if this is an array, it will be turned into a comma delimited string
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
 * Transforms an array into a comma delimited string
 * 
 * @param array $list A list of values to transform
 * @return string
 */
function commas(array $list): string
{
    return implode(', ', $list);
}

/**
 * Transforms an array into a space delimited string
 * 
 * @param array $list A list of values to transform
 * @return string
 */
function spaces(array $list): string
{
    return implode(' ', $list);
}

/**
 * Returns a DSN to be used with PDO's constructor
 * 
 * @param string $host      MySQL host name e.g. localhost
 * @param string $database  MySQL database name
 * @param array  $arguments PDO arguments
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
