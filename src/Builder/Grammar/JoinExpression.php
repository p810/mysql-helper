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
     * @var null|string
     */
    public $alias;

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
     * @param \p810\MySQL\Builder\BuilderInterface The builder class associated with this expression
     * @param string $type The type of join being appended
     * @param mixed|\p810\MySQL\Builder\BuilderInterface $table The table to fetch data from, or a subquery
     * @param null|string $alias An optional alias for the table name
     * @return void
     */
    function __construct(BuilderInterface $builder, string $type, $table, ?string $alias = null)
    {
        $this->type = $type;
        $this->table = $table;
        $this->alias = $alias;
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
            $this->getTable(),
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
     * Returns the expression's table formatted for the query
     * 
     * If an alias was specified, it will be appended to the table name
     * 
     * @return string
     */
    public function getTable(): string
    {
        $table = $this->table;

        if ($this->alias) {
            $table .= " as $this->alias";
        }

        return $table;
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
     * @param mixed|\p810\MySQL\Builder\BuilderInterface $left The left hand side of the expression
     * @param mixed|\p810\MySQL\Builder\BuilderInterface $right The right hand side of the expression
     * @param string $operator The comparison operator (middle of the expression)
     * @param string $logical A logical operator to concatenate this clause to one before it, if needed
     * @return \p810\MySQL\Builder\BuilderInterface
     */
    public function on($left, $right, string $operator = '=', string $logical = 'and'): BuilderInterface
    {
        if (! $this->method) {
            $this->method = 'on';
        }

        $this->predicates[] = new Expression($left, $right, $operator, $logical);

        return $this->builder;
    }
}
