<?php

namespace p810\MySQL\Builder;

interface BuilderInterface
{
    /**
     * Binds a value for use in a prepared query
     *
     * @param string|array $value The value(s) to bind
     * @return string|array
     */
    public function bind($value);

    /**
     * Checks if a given value is an instance of `\p810\MySQL\Builder\BuilderInterface` and, if so, builds it and
     * pushes its bindings onto the current query's input array, then returns the query string. Otherwise the value is
     * returned as is.
     * 
     * @param mixed|\p810\MySQL\Builder\BuilderInterface $value Any part of a query that may be a subquery that should
     *                                                          be compiled
     * @return array|int|string
     */
    public function prepare($value);

    /**
     * Compiles a SQL query from the current object's state
     * 
     * @return string
     */
    public function build(): string;

    /**
     * Returns an array of bound parameters for the query
     * 
     * @return array
     */
    public function getInput(): array;

    /**
     * Returns the name of the SQL command the class represents, or null if there is none
     * 
     * @return null|string
     */
    public function getCommand(): ?string;

    /**
     * Returns a value for the given parameter if it's been set, or null otherwise
     *
     * @param string $param The name of the parameter to get
     * @return null|mixed
     * @psalm-ignore-nullable-return
     */
    public function getParameter(string $param);

    /**
     * Sets a parameter to the given value
     * 
     * @param string $param The parameter's name
     * @param mixed $value A value for the parameter
     * @return \p810\MySQL\Builder\BuilderInterface
     */
    public function setParameter(string $param, $value): BuilderInterface;

    /**
     * Adds the given alias to an array of aliases, optionally associated to a given table
     * 
     * @param string $alias A table's alias
     * @param null|string $table An optional table name
     * @return \p810\MySQL\Builder\BuilderInterface
     */
    public function alias(string $alias, ?string $table = null): BuilderInterface;
}
