<?php

namespace p810\MySQL\Builder\Grammar;

use p810\MySQL\Builder\AbstractBuilder;

use function p810\MySQL\parentheses;

class ComplexWhere extends AbstractBuilder
{
    use Where;

    /**
     * Returns a nested where clause
     * 
     * @return string
     */
    function __toString(): string
    {
        $clauses = Expression::listToString($this->wheres);

        return parentheses($clauses);
    }

    /**
     * {@inheritdoc}
     */
    public function getCommand(): ?string
    {
        return null;
    }
}
