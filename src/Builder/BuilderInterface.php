<?php

namespace p810\MySQL\Builder;

/**
 * Represents a class that may be injected into a \p810\MySQL\Query to construct a query string.
 */
interface BuilderInterface
{
    public function build(): string;
}