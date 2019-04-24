<?php

namespace p810\MySQL\Builder;

use PDO;
use PDOStatement;

use function ucfirst;
use function is_array;
use function array_map;
use function array_walk;
use function array_reduce;
use function p810\MySQL\spaces;

abstract class Builder
{
    /**
     * @var string[]
     */
    protected $components;

    /**
     * @var array
     */
    public $input = [];

    /**
     * Binds a value for use in a prepared query
     * 
     * @param array|string|int $value The value to bind
     * @return string|array
     */
    public function bind($value)
    {
        if (is_array($value)) {
            return array_map(function ($value) {
                return $this->bind($value);
            }, $value);
        }

        $this->input[] = $value;

        return '?';
    }

    /**
     * Compiles a query
     * 
     * Each builder specifies a list of $components which will be iterated
     * to get a compiler method, e.g. compileFrom(). The result of that call
     * (if applicable) is then appended to a list of strings that will be 
     * joined by spaces to form the query string.
     * 
     * @return string
     */
    public function build(): string
    {
        $parts = array_reduce($this->components, function ($value, $component) {
            $method = 'compile' . ucfirst($component);
            $result = $this->$method();

            if ($result) {
                $value[] = $result;
            }

            return $value;
        }, []);

        return spaces($parts);
    }

    /**
     * Processes the result of the query
     * 
     * @param \PDOStatement $statement
     * @return mixed
     */
    abstract public function process(PDOStatement $statement);

    /**
     * Prefixes column names with their corresponding tables, e.g. for a
     * query that joins data from foreign tables
     * 
     * @param array $columns An associative array of tables => columns
     * @return string[]
     */
    protected function prefixColumns(array $columns): array
    {
        array_walk($columns, function (&$column, $table) {
            $column = "$table.$column";
        });

        return $columns;
    }

    /**
     * Returns the number of rows affected by a query
     * 
     * @param \PDOStatement $statement Result of the query
     * @return int
     */
    public function getRowCount(PDOStatement $statement): int
    {
        return $statement->rowCount($statement);
    }

    /**
     * Returns a result set as a list of associative arrays
     * 
     * @param \PDOStatement $statement Result of the query
     * @return array
     */
    public function getResultSet(PDOStatement $statement): array
    {
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }
}