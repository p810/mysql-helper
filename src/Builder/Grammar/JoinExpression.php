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
     * @psalm-suppress PropertyNotSetInConstructor
     */
    public $method;

    /**
     * @var string
     * @psalm-suppress PropertyNotSetInConstructor
     */
    public $column;

    /**
     * @var \p810\MySQL\Builder\Grammar\Expression[]
     * @psalm-suppress PropertyNotSetInConstructor
     */
    public $predicates;

    /**
     * Constructs the expression with the given join type (e.g. inner, left) and table
     * 
     * @param string $type The type of join being appended
     * @param string $table The table to fetch data from
     * @return void
     */
    function __construct(string $type, string $table)
    {
        $this->type  = $type;
        $this->table = $table;
    }

    /**
     * Returns the join expression as a string
     * 
     * @return string
     */
    function __toString(): string
    {
        return sprintf('%s join %s %s %s', 
            $this->type,
            $this->table,
            $this->method,
            $this->getPredicate()
        );
    }

    /**
     * Returns either a "using (...)" clause, or a list of "x on y" clauses
     * 
     * @return string
     */
    public function getPredicate(): string
    {
        if ($this->method == 'using') {
            return parentheses($this->column);
        }

        return Expression::listToString($this->predicates);
    }

    /**
     * Sets a "using (...)" clause for the join
     * 
     * @param string $column The column that both tables have in common
     * @return void
     */
    public function using(string $column): void
    {
        if (! $this->method) {
            $this->method = 'using';
        }

        $this->column = $column;
    }

    /**
     * Sets an "on" clause for the join
     * 
     * @param string $left The left hand side of the clause (a column)
     * @param string $right The right hand side of the clause (another column)
     * @param string $logical A logical operator to concatenate the clauses
     * @return void
     */
    public function on(
        string $left,
        string $right,
        string $logical = 'and'
    ): void {
        if (! $this->method) {
            $this->method = 'on';
        }

        $this->predicates[] = new Expression($left, $right, '=', $logical);
    }
}
