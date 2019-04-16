<?php

namespace p810\MySQL\Builder\Grammar;

use function sprintf;
use function p810\MySQL\parentheses;

class JoinExpression
{
    /**
     * @var string
     */
    public $type;

    /**
     * @var string
     */
    public $table;

    /**
     * @var string
     */
    public $method;

    /**
     * @var string
     */
    public $column;

    /**
     * @var \p810\MySQL\Builder\Grammar\Expression[]
     */
    public $predicates;

    function __construct(string $type, string $table)
    {
        $this->type  = $type;
        $this->table = $table;
    }
    
    function __toString(): string
    {
        return sprintf('%s join %s %s %s', 
            $this->type,
            $this->table,
            $this->method,
            $this->getPredicate()
        );
    }

    public function getPredicate(): string
    {
        if ($this->method == 'using') {
            return parentheses($this->column);
        }

        return Expression::listToString($this->predicates);
    }

    public function using(string $column): void
    {
        if (! $this->method) {
            $this->method = 'using';
        }

        $this->column = $column;
    }

    public function on(string $left, string $right, string $logical = 'and'): void
    {
        if (! $this->method) {
            $this->method = 'on';
        }

        $this->predicates[] = new Expression($left, $right, '=', $logical);
    }
}