<?php

namespace p810\MySQL\Builder\Grammar;

use BadMethodCallException;
use p810\MySQL\Builder\BuilderInterface;

use function p810\MySQL\spaces;

trait Join
{
    /**
     * @var null|\p810\MySQL\Builder\Grammar\JoinExpression
     */
    private $current;

    /**
     * Appends a join to the query
     * 
     * @param string $type The type of join (e.g. inner, left)
     * @param string|\p810\MySQL\Builder\BuilderInterface $table The table to join data from
     * @param null|string $alias An optional alias for the table
     * @return \p810\MySQL\Builder\BuilderInterface
     */
    protected function join(string $type, $table, ?string $alias = null): BuilderInterface
    {
        $joins = $this->getParameter('joins') ?? [];

        $table = $this->prepare($table);

        if ($alias) {
            $this->alias($alias, $table);
        }

        $this->setCurrentTable($table);

        $this->current = $joins[] = new JoinExpression($this, $type, $table, $alias);

        return $this->setParameter('joins', $joins);
    }

    /**
     * Appends an inner join to the query
     * 
     * @param string|\p810\MySQL\Builder\BuilderInterface $table The table to join data from
     * @param null|string $alias An optional alias for the table
     * @return \p810\MySQL\Builder\BuilderInterface
     */
    public function innerJoin($table, ?string $alias = null): BuilderInterface
    {
        return $this->join('inner', $table, $alias);
    }

    /**
     * Appends a left join to the query
     * 
     * @param string|\p810\MySQL\Builder\BuilderInterface $table The table to join data from
     * @param null|string $alias An optional alias for the table
     * @return \p810\MySQL\Builder\BuilderInterface
     */
    public function leftJoin($table, ?string $alias = null): BuilderInterface
    {
        return $this->join('left', $table, $alias);
    }

    /**
     * Appends a right join to the query
     * 
     * @param string|\p810\MySQL\Builder\BuilderInterface $table The table to join data from
     * @param null|string $alias An optional alias for the table
     * @return \p810\MySQL\Builder\BuilderInterface
     */
    public function rightJoin($table, ?string $alias = null): BuilderInterface
    {
        return $this->join('right', $table, $alias);
    }

    /**
     * Appends a left outer join to the query
     * 
     * @param string|\p810\MySQL\Builder\BuilderInterface $table The table to join data from
     * @param null|string $alias An optional alias for the table
     * @return \p810\MySQL\Builder\BuilderInterface
     */
    public function leftOuterJoin($table, ?string $alias = null): BuilderInterface
    {
        return $this->join('left outer', $table, $alias);
    }

    /**
     * Appends a right outer join to the query
     * 
     * @param string|\p810\MySQL\Builder\BuilderInterface $table The table to join data from
     * @param null|string $alias An optional alias for the table
     * @return \p810\MySQL\Builder\BuilderInterface
     */
    public function rightOuterJoin($table, ?string $alias = null): BuilderInterface
    {
        return $this->join('right outer', $table, $alias);
    }

    /**
     * Sets a "using (...)" clause for the current `\p810\MySQL\Builder\Grammar\JoinExpression`
     * 
     * @param string $column The column that both tables have in common
     * @return \p810\MySQL\Builder\BuilderInterface
     * @psalm-suppress PossiblyNullReference
     */
    public function using(string $column): BuilderInterface
    {
        $this->throwIfCalledBeforeSetter('using');

        return $this->current->using($column);
    }

    /**
     * Appends an "on" clause to the current `\p810\MySQL\Builder\Grammar\JoinExpression`
     * 
     * @param mixed|\p810\MySQL\Builder\BuilderInterface $left The left hand side of the expression
     * @param mixed|\p810\MySQL\Builder\BuilderInterface $right The right hand side of the expression
     * @param string $operator The concatenating operator between the left and righthand values
     * @param string $logical The logical operator used to concatenate this expression to one before it
     * @return \p810\MySQL\Builder\BuilderInterface
     * @throws \BadMethodCallException if this method was called before a `JoinExpression` was instantiated
     * @psalm-suppress PossiblyNullReference
     */
    public function on($left, $right, string $operator = '=', string $logical = 'and'): BuilderInterface
    {
        $this->throwIfCalledBeforeSetter('on');

        [$left, $right] = $this->prepare([$left, $right]);

        return $this->current->on($left, $right, $operator, $logical);
    }

    /**
     * Appends an "on" clause to the current `\p810\MySQL\Builder\Grammar\JoinExpression` with "or" as the logical
     * operator
     * 
     * @param mixed|\p810\MySQL\Builder\BuilderInterface $left The left hand side of the expression
     * @param mixed|\p810\MySQL\Builder\BuilderInterface $right The right hand side of the expression
     * @param string $operator The concatenating operator between the left and righthand values
     * @return \p810\MySQL\Builder\BuilderInterface
     * @throws \BadMethodCallException if this method was called before a `JoinExpression` was instantiated
     * @psalm-suppress PossiblyNullReference
     */
    public function orOn($left, $right, string $operator = '='): BuilderInterface
    {
        return $this->on($left, $right, $operator, 'or');
    }

    /**
     * Appends an "on" clause to the current `\p810\MySQL\Builder\Grammar\JoinExpression` with "!=" as the operator
     * 
     * @param mixed|\p810\MySQL\Builder\BuilderInterface $left The left hand side of the expression
     * @param mixed|\p810\MySQL\Builder\BuilderInterface $right The right hand side of the expression
     * @param string $logical The logical operator used to concatenate this expression to one before it
     * @return \p810\MySQL\Builder\BuilderInterface
     * @throws \BadMethodCallException if this method was called before a `JoinExpression` was instantiated
     * @psalm-suppress PossiblyNullReference
     */
    public function onNotEquals($left, $right, string $logical = 'and'): BuilderInterface
    {
        return $this->on($left, $right, '!=', $logical);
    }

    /**
     * An alias for `\p810\MySQL\Builder\Grammar\Join::onNotEquals()`
     * 
     * @codeCoverageIgnore
     * @param mixed|\p810\MySQL\Builder\BuilderInterface $left The left hand side of the expression
     * @param mixed|\p810\MySQL\Builder\BuilderInterface $right The right hand side of the expression
     * @param string $logical The logical operator used to concatenate this expression to the one before it
     * @return \p810\MySQL\Builder\BuilderInterface
     * @throws \BadMethodCallException if this method was called before a `JoinExpression` was instantiated
     */
    public function onNot($left, $right, string $logical = 'and'): BuilderInterface
    {
        return $this->onNotEquals($left, $right, $logical);
    }

    /**
     * Appends an "on" clause to the current `\p810\MySQL\Builder\Grammar\JoinExpression` with "!=" as the operator and
     * "or" as the logical operator
     * 
     * @param mixed|\p810\MySQL\Builder\BuilderInterface $left The left hand side of the expression
     * @param mixed|\p810\MySQL\Builder\BuilderInterface $right The right hand side of the expression
     * @return \p810\MySQL\Builder\BuilderInterface
     * @throws \BadMethodCallException if this method was called before a `JoinExpression` was instantiated
     * @psalm-suppress PossiblyNullReference
     */
    public function orOnNotEquals($left, $right): BuilderInterface
    {
        return $this->onNotEquals($left, $right, 'or');
    }

    /**
     * An alias for `\p810\MySQL\Builder\Grammar\Join::orOnNotEquals()`
     * 
     * @codeCoverageIgnore
     * @param mixed|\p810\MySQL\Builder\BuilderInterface $left The left hand side of the expression
     * @param mixed|\p810\MySQL\Builder\BuilderInterface $right The right hand side of the expression
     * @return \p810\MySQL\Builder\BuilderInterface
     * @throws \BadMethodCallException if this method was called before a `JoinExpression` was instantiated
     */
    public function orOnNot($left, $right): BuilderInterface
    {
        return $this->orOnNotEquals($left, $right);
    }

    /**
     * Appends an "on" clause to the current `\p810\MySQL\Builder\Grammar\JoinExpression` with "like" as the operator
     * 
     * @param mixed|\p810\MySQL\Builder\BuilderInterface $left The left hand side of the expression
     * @param mixed|\p810\MySQL\Builder\BuilderInterface $right The right hand side of the expression
     * @param string $logical The logical operator used to concatenate this expression to one before it
     * @return \p810\MySQL\Builder\BuilderInterface
     * @throws \BadMethodCallException if this method was called before a `JoinExpression` was instantiated
     * @psalm-suppress PossiblyNullReference
     */
    public function onLike($left, $right, string $logical = 'and'): BuilderInterface
    {
        return $this->on($left, $right, 'like', $logical);
    }

    /**
     * Appends an "on" clause to the current `\p810\MySQL\Builder\Grammar\JoinExpression` with "like" as the operator
     * and "or" as the logical operator
     * 
     * @param mixed|\p810\MySQL\Builder\BuilderInterface $left The left hand side of the expression
     * @param mixed|\p810\MySQL\Builder\BuilderInterface $right The right hand side of the expression
     * @return \p810\MySQL\Builder\BuilderInterface
     * @throws \BadMethodCallException if this method was called before a `JoinExpression` was instantiated
     * @psalm-suppress PossiblyNullReference
     */
    public function orOnLike($left, $right): BuilderInterface
    {
        return $this->onLike($left, $right, 'or');
    }

    /**
     * Appends an "on" clause to the current `\p810\MySQL\Builder\Grammar\JoinExpression` with "not like" as the
     * operator
     * 
     * @param mixed|\p810\MySQL\Builder\BuilderInterface $left The left hand side of the expression
     * @param mixed|\p810\MySQL\Builder\BuilderInterface $right The right hand side of the expression
     * @param string $logical The logical operator used to concatenate this expression to one before it
     * @return \p810\MySQL\Builder\BuilderInterface
     * @throws \BadMethodCallException if this method was called before a `JoinExpression` was instantiated
     * @psalm-suppress PossiblyNullReference
     */
    public function onNotLike($left, $right, string $logical = 'and'): BuilderInterface
    {
        return $this->on($left, $right, 'not like', $logical);
    }

    /**
     * Appends an "on" clause to the current `\p810\MySQL\Builder\Grammar\JoinExpression` with "not like" as the
     * operator and "or" as the logical operator
     * 
     * @param mixed|\p810\MySQL\Builder\BuilderInterface $left The left hand side of the expression
     * @param mixed|\p810\MySQL\Builder\BuilderInterface $right The right hand side of the expression
     * @return \p810\MySQL\Builder\BuilderInterface
     * @throws \BadMethodCallException if this method was called before a `JoinExpression` was instantiated
     * @psalm-suppress PossiblyNullReference
     */
    public function orOnNotLike($left, $right): BuilderInterface
    {
        return $this->onNotLike($left, $right, 'or');
    }

    /**
     * Raises an exception if the given method was called before a setter method (e.g. `innerJoin()`) was
     * 
     * @throws \BadMethodCallException if the given method was called before a `JoinExpression` was instantiated
     */
    protected function throwIfCalledBeforeSetter(string $method): void
    {
        if (! $this->current) {
            throw new BadMethodCallException(
                "\p810\MySQL\Builder\Grammar\Join::$method() cannot be called before an expression has been set by " .
                "calling a setter method, e.g. \p810\MySQL\Builder\Grammar\Join::innerJoin()"
            );
        }
    }

    /**
     * Compiles the join clause
     * 
     * @return null|string
     */
    protected function compileJoin(): ?string
    {
        $joins = $this->getParameter('joins');

        if (! $joins) {
            return null;
        }

        foreach ($joins as $expression) {
            $table = $expression->table;
            $alias = $this->getTableAlias($table);

            if ($alias) {
                $expression->alias = $alias;
            }
        }

        return spaces($joins);
    }
}
