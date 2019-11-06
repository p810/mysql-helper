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
    public function getParameters(): array;

    /**
     * Returns the name of the SQL command the class represents, or null if there is none
     * 
     * @return null|string
     */
    public function getCommand(): ?string;
}
