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
     * @param string $table The table to join data from 
     * @return \p810\MySQL\Builder\BuilderInterface
     */
    protected function join(string $type, string $table): BuilderInterface
    {
        $joins = $this->getParameter('joins') ?? [];

        $this->current = $joins[] = new JoinExpression($type, $table, $this);

        return $this->setParameter('joins', $joins);
    }

    /**
     * Appends an inner join to the query
     * 
     * @param string $table The table to join data from
     * @return \p810\MySQL\Builder\BuilderInterface
     */
    public function innerJoin(string $table): BuilderInterface
    {
        return $this->join('inner', $table);
    }

    /**
     * Appends a left join to the query
     * 
     * @param string $table The table to join data from
     * @return \p810\MySQL\Builder\BuilderInterface
     */
    public function leftJoin(string $table): BuilderInterface
    {
        return $this->join('left', $table);
    }

    /**
     * Appends a right join to the query
     * 
     * @param string $table The table to join data from
     * @return \p810\MySQL\Builder\BuilderInterface
     */
    public function rightJoin(string $table): BuilderInterface
    {
        return $this->join('right', $table);
    }

    /**
     * Appends a left outer join to the query
     * 
     * @param string $table The table to join data from
     * @return \p810\MySQL\Builder\BuilderInterface
     */
    public function leftOuterJoin(string $table): BuilderInterface
    {
        return $this->join('left outer', $table);
    }

    /**
     * Appends a right outer join to the query
     * 
     * @param string $table The table to join data from
     * @return \p810\MySQL\Builder\BuilderInterface
     */
    public function rightOuterJoin(string $table): BuilderInterface
    {
        return $this->join('right outer', $table);
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
     * @param string $left The left hand column
     * @param string $right The right hand column
     * @param string $operator The concatenating operator between the left and righthand values
     * @param string $logical The logical operator used to concatenate this expression to one before it
     * @return \p810\MySQL\Builder\BuilderInterface
     * @throws \BadMethodCallException if this method was called before a `JoinExpression` was instantiated
     * @psalm-suppress PossiblyNullReference
     */
    public function on(string $left, string $right, string $operator = '=', string $logical = 'and'): BuilderInterface
    {
        $this->throwIfCalledBeforeSetter('on');

        return $this->current->on($left, $right, $operator, $logical);
    }

    /**
     * Appends an "on" clause to the current `\p810\MySQL\Builder\Grammar\JoinExpression` with "or" as the logical
     * operator
     * 
     * @param string $left The left hand column
     * @param string $right The right hand column
     * @param string $operator The concatenating operator between the left and righthand values
     * @return \p810\MySQL\Builder\BuilderInterface
     * @throws \BadMethodCallException if this method was called before a `JoinExpression` was instantiated
     * @psalm-suppress PossiblyNullReference
     */
    public function orOn(string $left, string $right, string $operator = '='): BuilderInterface
    {
        $this->throwIfCalledBeforeSetter('orOn');

        return $this->current->on($left, $right, $operator, 'or');
    }

    /**
     * Appends an "on" clause to the current `\p810\MySQL\Builder\Grammar\JoinExpression` with "!=" as the operator
     * 
     * @param string $left The left hand column
     * @param string $right The right hand column
     * @param string $logical The logical operator used to concatenate this expression to one before it
     * @return \p810\MySQL\Builder\BuilderInterface
     * @throws \BadMethodCallException if this method was called before a `JoinExpression` was instantiated
     * @psalm-suppress PossiblyNullReference
     */
    public function onNotEquals(string $left, string $right, string $logical = 'and'): BuilderInterface
    {
        $this->throwIfCalledBeforeSetter('orNotEquals');

        return $this->current->on($left, $right, '!=', $logical);
    }

    /**
     * An alias for `\p810\MySQL\Builder\Grammar\Join::onNotEquals()`
     * 
     * @codeCoverageIgnore
     * @param string $left The left hand column
     * @param string $right The right hand column
     * @param string $logical The logical operator used to concatenate this expression to the one before it
     * @return \p810\MySQL\Builder\BuilderInterface
     * @throws \BadMethodCallException if this method was called before a `JoinExpression` was instantiated
     */
    public function onNot(string $left, string $right, string $logical = 'and'): BuilderInterface
    {
        return $this->onNotEquals($left, $right, $logical);
    }

    /**
     * Appends an "on" clause to the current `\p810\MySQL\Builder\Grammar\JoinExpression` with "!=" as the operator and
     * "or" as the logical operator
     * 
     * @param string $left The left hand column
     * @param string $right The right hand column
     * @return \p810\MySQL\Builder\BuilderInterface
     * @throws \BadMethodCallException if this method was called before a `JoinExpression` was instantiated
     * @psalm-suppress PossiblyNullReference
     */
    public function orOnNotEquals(string $left, string $right): BuilderInterface
    {
        $this->throwIfCalledBeforeSetter('orOnNotEquals');

        return $this->current->on($left, $right, '!=', 'or');
    }

    /**
     * An alias for `\p810\MySQL\Builder\Grammar\Join::orOnNotEquals()`
     * 
     * @codeCoverageIgnore
     * @param string $left The left hand column
     * @param string $right The right hand column
     * @return \p810\MySQL\Builder\BuilderInterface
     * @throws \BadMethodCallException if this method was called before a `JoinExpression` was instantiated
     */
    public function orOnNot(string $left, string $right): BuilderInterface
    {
        return $this->orOnNotEquals($left, $right);
    }

    /**
     * Appends an "on" clause to the current `\p810\MySQL\Builder\Grammar\JoinExpression` with "like" as the operator
     * 
     * @param string $left The left hand column
     * @param string $right The right hand column
     * @param string $logical The logical operator used to concatenate this expression to one before it
     * @return \p810\MySQL\Builder\BuilderInterface
     * @throws \BadMethodCallException if this method was called before a `JoinExpression` was instantiated
     * @psalm-suppress PossiblyNullReference
     */
    public function onLike(string $left, string $right, string $logical = 'and'): BuilderInterface
    {
        $this->throwIfCalledBeforeSetter('onLike');

        return $this->current->on($left, $right, 'like', $logical);
    }

    /**
     * Appends an "on" clause to the current `\p810\MySQL\Builder\Grammar\JoinExpression` with "like" as the operator
     * and "or" as the logical operator
     * 
     * @param string $left The left hand column
     * @param string $right The right hand column
     * @return \p810\MySQL\Builder\BuilderInterface
     * @throws \BadMethodCallException if this method was called before a `JoinExpression` was instantiated
     * @psalm-suppress PossiblyNullReference
     */
    public function orOnLike(string $left, string $right): BuilderInterface
    {
        $this->throwIfCalledBeforeSetter('orOnLike');

        return $this->current->on($left, $right, 'like', 'or');
    }

    /**
     * Appends an "on" clause to the current `\p810\MySQL\Builder\Grammar\JoinExpression` with "not like" as the
     * operator
     * 
     * @param string $left The left hand column
     * @param string $right The right hand column
     * @param string $logical The logical operator used to concatenate this expression to one before it
     * @return \p810\MySQL\Builder\BuilderInterface
     * @throws \BadMethodCallException if this method was called before a `JoinExpression` was instantiated
     * @psalm-suppress PossiblyNullReference
     */
    public function onNotLike(string $left, string $right, string $logical = 'and'): BuilderInterface
    {
        $this->throwIfCalledBeforeSetter('onNotLike');

        return $this->current->on($left, $right, 'not like', $logical);
    }

    /**
     * Appends an "on" clause to the current `\p810\MySQL\Builder\Grammar\JoinExpression` with "not like" as the
     * operator and "or" as the logical operator
     * 
     * @param string $left The left hand column
     * @param string $right The right hand column
     * @return \p810\MySQL\Builder\BuilderInterface
     * @throws \BadMethodCallException if this method was called before a `JoinExpression` was instantiated
     * @psalm-suppress PossiblyNullReference
     */
    public function orOnNotLike(string $left, string $right): BuilderInterface
    {
        $this->throwIfCalledBeforeSetter('orOnNotLike');

        return $this->current->on($left, $right, 'not like', 'or');
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

        return spaces($joins);
    }
}
