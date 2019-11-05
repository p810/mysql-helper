<?php

namespace p810\MySQL\Builder;

use function ucfirst;
use function is_array;
use function array_map;
use function array_reduce;
use function p810\MySQL\spaces;

abstract class AbstractBuilder implements BuilderInterface
{
    /**
     * @var string
     */
    const COMMAND = '';

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
     * Each builder specifies a list of $components which will be iterated to get a compiler method, for example
     * `compileFrom()`. The result of that call (if applicable) is then appended to a list of strings that will be 
     * joined (with spaces) to form the query string.
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
     * Returns the query as a string
     * 
     * @return string
     */
    function __toString(): string
    {
        return $this->build();
    }
}
