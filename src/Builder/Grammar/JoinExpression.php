<?php

namespace p810\MySQL\Builder\Grammar;

use p810\MySQL\Builder\BuilderInterface;

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
     * @var \p810\MySQL\Builder\BuilderInterface
     */
    protected $builder;

    /**
     * Constructs the expression with the given join type (e.g. inner, left) and table
     * 
     * @param string $type The type of join being appended
     * @param string $table The table to fetch data from
     * @param \p810\MySQL\Builder\BuilderInterface The builder class associated with this expression
     * @return void
     */
    function __construct(string $type, string $table, BuilderInterface $builder)
    {
        $this->type = $type;
        $this->table = $table;
        $this->builder = $builder;
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
     * @return \p810\MySQL\Builder\BuilderInterface
     */
    public function using(string $column): BuilderInterface
    {
        if (! $this->method) {
            $this->method = 'using';
        }

        $this->column = $column;

        return $this->builder;
    }

    /**
     * Sets an "on" clause for the join
     * 
     * @param string $left The left hand side of the clause (a column)
     * @param string $right The right hand side of the clause (another column)
     * @param string $operator The comparison operator (middle of the expression)
     * @param string $logical A logical operator to concatenate this clause to one before it, if needed
     * @return \p810\MySQL\Builder\BuilderInterface
     */
    public function on(string $left, string $right, string $operator = '=', string $logical = 'and'): BuilderInterface
    {
        if (! $this->method) {
            $this->method = 'on';
        }

        $this->predicates[] = new Expression($left, $right, $operator, $logical);

        return $this->builder;
    }
}
