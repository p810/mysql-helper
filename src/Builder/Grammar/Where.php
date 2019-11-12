<?php

namespace p810\MySQL\Builder\Grammar;

use p810\MySQL\Query;
use p810\MySQL\Builder\BuilderInterface;

use function p810\MySQL\parentheses;

trait Where
{
    /**
     * @var array<int,\p810\MySQL\Builder\Grammar\Expression|string>
     */
    protected $wheres = [];

    /**
     * Appends an expression to the where clause
     * 
     * @param string $column Left hand side of the expression (column)
     * @param mixed  $value Right hand side of the expression (value)
     * @param string $operator Middle of the expression (comparison operator)
     * @param string $logical A logical operator used to concatenate the expression in the clause
     * @return \p810\MySQL\Builder\BuilderInterface
     */
    public function where(string $column, $value, string $operator = '=', string $logical = 'and'): BuilderInterface
    {
        $wheres = $this->getParameter('where') ?? [];

        $wheres[] = new Expression(
            $column,
            $this->prepareValue($value),
            $operator,
            $logical
        );

        return $this->setParameter('where', $wheres);
    }

    /**
     * Prepares a value for a `WHERE` clause
     * 
     * If the provided value was an instance of `\p810\MySQL\Query`, we treat it as a subquery and compile it.
     * Otherwise if it's scalar we bind it and return a placeholder.
     * 
     * @param mixed $value
     * @return string|array
     */
    protected function prepareValue($value)
    {
        if ($value instanceof Query) {
            return parentheses($value->build());
        }

        return $this->bind($value);
    }

    /**
     * Appends an expression with "or" as the logical operator
     * 
     * @param string $column Left hand side of the expression (column)
     * @param mixed  $value Right hand side of the expression (value)
     * @param string $operator Middle of the expression (comparison operator)
     * @return \p810\MySQL\Builder\BuilderInterface
     */
    public function orWhere(string $column, $value, string $operator = '='): BuilderInterface
    {
        return $this->where($column, $value, $operator, 'or');
    }

    /**
     * Appends an expression with "!=" as the comparison operator
     * 
     * @param string $column Left hand side of the expression (column)
     * @param mixed  $value Right hand side of the expression (value)
     * @param string $logical A logical operator used to concatenate the expression in the clause
     * @return \p810\MySQL\Builder\BuilderInterface
     */
    public function whereNotEquals(string $column, $value, string $logical = 'and'): BuilderInterface
    {
        return $this->where($column, $value, '!=', $logical);
    }

    /**
     * An alias for `\p810\MySQL\Builder\Grammar\Where::whereNotEquals()`
     * 
     * @param string $column Lefthand side of the expression (column)
     * @param mixed  $value Righthand side of the expression (value)
     * @param string $logical A logical operator used to concatenate the expression in the clause
     * @return \p810\MySQL\Builder\BuilderInterface
     */
    public function whereNot(string $column, $value, string $logical = 'and'): BuilderInterface
    {
        return $this->whereNotEquals($column, $value, $logical);
    }

    /**
     * An alias for `\p810\MySQL\Builder\Grammar\Where::whereNotEquals()`, but specifies "or" as the logical operator
     * 
     * @codeCoverageIgnore
     * @param string $column Left hand side of the expression (column)
     * @param mixed  $value Right hand side of the expression (value)
     * @return \p810\MySQL\Builder\BuilderInterface
     */
    public function orWhereNotEquals(string $column, $value): BuilderInterface
    {
        return $this->whereNotEquals($column, $value, 'or');
    }

    /**
     * An alias for `\p810\MySQL\Builder\Grammar\Where::orWhereNotEquals()`
     * 
     * @codeCoverageIgnore
     * @param string $column Left hand side of the expression (column)
     * @param mixed  $value Right hand side of the expression (value)
     * @return \p810\MySQL\Builder\BuilderInterface
     */
    public function orWhereNot(string $column, $value): BuilderInterface
    {
        return $this->orWhereNotEquals($column, $value);
    }

    /**
     * Appends an expression with "<" as the comparison operator
     * 
     * @param string $column Left hand side of the expression (column)
     * @param mixed  $value Right hand side of the expression (value)
     * @param string $logical A logical operator used to concatenate the expression in the clause
     * @return \p810\MySQL\Builder\BuilderInterface
     */
    public function whereLess(string $column, $value, string $logical = 'and'): BuilderInterface
    {
        return $this->where($column, $value, '<', $logical);
    }

    /**
     * An alias for `\p810\MySQL\Builder\Grammar\Where::whereLess()`, but specifies "or" as the logical operator
     * 
     * @param string $column Left hand side of the expression (column)
     * @param mixed  $value Right hand side of the expression (value)
     * @return \p810\MySQL\Builder\BuilderInterface
     */
    public function orWhereLess(string $column, $value): BuilderInterface
    {
        return $this->whereLess($column, $value, 'or');
    }

    /**
     * Appends an expression with "<=" as the comparison operator
     * 
     * @param string $column Left hand side of the expression (column)
     * @param mixed  $value Right hand side of the expression (value)
     * @param string $logical A logical operator used to concatenate the expression in the clause
     * @return \p810\MySQL\Builder\BuilderInterface
     */
    public function whereLessOrEqual(string $column, $value, string $logical = 'and'): BuilderInterface
    {
        return $this->where($column, $value, '<=', $logical);
    }

    /**
     * An alias for `\p810\MySQL\Builder\Grammar\Where::whereLessOrEqual()`, but specifies "or" as the logical operator
     * 
     * @codeCoverageIgnore
     * @param string $column Left hand side of the expression (column)
     * @param mixed  $value Right hand side of the expression (value)
     * @return \p810\MySQL\Builder\BuilderInterface
     */
    public function orWhereLessOrEqual(string $column, $value): BuilderInterface
    {
        return $this->whereLessOrEqual($column, $value, 'or');
    }

    /**
     * Appends an expression with ">" as the comparison operator
     * 
     * @param string $column Left hand side of the expression (column)
     * @param mixed  $value Right hand side of the expression (value)
     * @param string $logical A logical operator used to concatenate the expression in the clause
     * @return \p810\MySQL\Builder\BuilderInterface
     */
    public function whereGreater(string $column, $value, string $logical = 'and'): BuilderInterface
    {
        return $this->where($column, $value, '>', $logical);
    }

    /**
     * An alias for `\p810\MySQL\Builder\Grammar\Where::whereGreater()`, but specifies "or" as the logical operator
     * 
     * @codeCoverageIgnore
     * @param string $column Left hand side of the expression (column)
     * @param mixed  $value Right hand side of the expression (value)
     * @return \p810\MySQL\Builder\BuilderInterface
     */
    public function orWhereGreater(string $column, $value): BuilderInterface
    {
        return $this->whereGreater($column, $value, 'or');
    }

    /**
     * Appends an expression with ">=" as the comparison operator
     * 
     * @param string $column Left hand side of the expression (column)
     * @param mixed  $value Right hand side of the expression (value)
     * @param string $logical A logical operator used to concatenate the expression in the clause
     * @return \p810\MySQL\Builder\BuilderInterface
     */
    public function whereGreaterOrEqual(string $column, $value, string $logical = 'and'): BuilderInterface
    {
        return $this->where($column, $value, '>=', $logical);
    }

    /**
     * An alias for `\p810\MySQL\Builder\Grammar\Where::whereGreaterOrEqual()` that specifies "or" as the logical
     * operator
     * 
     * @codeCoverageIgnore
     * @param string $column Left hand side of the expression (column)
     * @param mixed  $value Right hand side of the expression (value)
     * @return \p810\MySQL\Builder\BuilderInterface
     */
    public function orWhereGreaterOrEqual(string $column, $value): BuilderInterface
    {
        return $this->whereGreaterOrEqual($column, $value, 'or');
    }

    /**
     * Appends an expression with "like" as the comparison operator
     * 
     * @param string $column Left hand side of the expression (column)
     * @param mixed  $value Right hand side of the expression (value)
     * @param string $logical A logical operator used to concatenate the expression in the clause
     * @return \p810\MySQL\Builder\BuilderInterface
     */
    public function whereLike(string $column, $value, string $logical = 'and'): BuilderInterface
    {
        return $this->where($column, $value, 'like', $logical);
    }

    /**
     * An alias for `\p810\MySQL\Builder\Grammar\Where::whereLike()` that specifies "or" as the logical operator
     * 
     * @codeCoverageIgnore
     * @param string $column Left hand side of the expression (column)
     * @param mixed  $value Right hand side of the expression (value)
     * @return \p810\MySQL\Builder\BuilderInterface
     */
    public function orWhereLike(string $column, $value): BuilderInterface
    {
        return $this->whereLike($column, $value, 'or');
    }

    /**
     * Appends an expression with "not like" as the comparison operator
     * 
     * @param string $column Left hand side of the expression (column)
     * @param mixed  $value Right hand side of the expression (value)
     * @param string $logical A logical operator used to concatenate the expression in the clause
     * @return \p810\MySQL\Builder\BuilderInterface
     */
    public function whereNotLike(string $column, $value, string $logical = 'and'): BuilderInterface
    {
        return $this->where($column, $value, 'not like', $logical);
    }

    /**
     * An alias for `\p810\MySQL\Builder\Grammar\Where::whereNotLike()` that specifies "or" as the logical operator
     * 
     * @codeCoverageIgnore
     * @param string $column Left hand side of the expression (column)
     * @param mixed  $value Right hand side of the expression (value)
     * @return \p810\MySQL\Builder\BuilderInterface
     */
    public function orWhereNotLike(string $column, $value): BuilderInterface
    {
        return $this->where($column, $value, 'not like', 'or');
    }

    /**
     * Appends an expression with "in" as the comparison operator
     * 
     * @param string $columnOrExpression Left hand side of the expression (column or an expression)
     * @param \p810\MySQL\Query|array $value Right hand side of the expression (a list of scalar values or subquery)
     * @param string $logical A logical operator used to concatenate the expression in the clause
     * @return \p810\MySQL\Builder\BuilderInterface
     */
    public function whereIn(string $columnOrExpression, $value, string $logical = 'and'): BuilderInterface
    {
        return $this->where($columnOrExpression, $value, 'in', $logical);
    }

    /**
     * An alias for `\p810\MySQL\Builder\Grammar\Where::whereIn()` that specifies "or" as the logical operator
     * 
     * @codeCoverageIgnore
     * @param string $columnOrExpression Left hand side of the expression (column or an expression)
     * @param \p810\MySQL\Query|array $value Right hand side of the expression (a list of scalar values or subquery)
     * @return \p810\MySQL\Builder\BuilderInterface
     */
    public function orWhereIn(string $columnOrExpression, $value): BuilderInterface
    {
        return $this->whereIn($columnOrExpression, $value, 'or');
    }

    /**
     * Appends an expression with "not in" as the comparison operator
     * 
     * @param string $columnOrExpression Left hand side of the expression (column or an expression)
     * @param \p810\MySQL\Query|array $value Right hand side of the expression (a list of scalar values or subquery)
     * @param string $logical A logical operator used to concatenate the expression in the clause
     * @return \p810\MySQL\Builder\BuilderInterface
     */
    public function whereNotIn(string $columnOrExpression, $value, string $logical = 'and'): BuilderInterface
    {
        return $this->where($columnOrExpression, $value, 'not in', $logical);
    }

    /**
     * An alias for `\p810\MySQL\Builder\Grammar\Where::whereNotIn()` that specifies "or" as the logical operator
     * 
     * @codeCoverageIgnore
     * @param string $columnOrExpression Left hand side of the expression (column or an expression)
     * @param \p810\MySQL\Query|array $value Right hand side of the expression (a list of scalar values or subquery)
     * @return \p810\MySQL\Builder\BuilderInterface
     */
    public function orWhereNotIn(string $columnOrExpression, $value): BuilderInterface
    {
        return $this->whereNotIn($columnOrExpression, $value, 'or');
    }

    /**
     * Appends a raw where clause to the list of expressions
     * 
     * @param string $clause The clause to append
     * @return \p810\MySQL\Builder\BuilderInterface
     */
    public function whereRaw(string $clause): BuilderInterface
    {
        $wheres = $this->getParameter('where') ?? [];

        $wheres[] = $clause;

        return $this->setParameter('where', $wheres);
    }

    /**
     * Appends a nested where clause
     * 
     * The callback that this method receives must take an instance of `p810\MySQL\Builder\Grammar\ComplexWhere` as its
     * first argument and return that object. An example of how that callback might look is:
     *
     * ```php
     * $query->whereNested(function (BuilderInterface $q) {
     *     return $q->where('foo', 'bar')->orWhere('bam', 'baz');
     * });
     * ```
     * 
     * This would generate the following: `(foo = ? or bar = ?)`
     * 
     * @param callable $cb A callback that should return a chain of clause calls
     * @param string $logical A logical operator used to concatenate the clause
     * @return \p810\MySQL\Builder\BuilderInterface
     */
    public function whereNested(callable $cb, string $logical = 'and'): BuilderInterface
    {
        $query = $cb(new ComplexWhere());

        $clause = $this->getParameter('where') ? "$logical $query" : "$query";

        return $this->whereRaw($clause);
    }

    /**
     * Appends a nested clause with "or" as the logical operator
     * 
     * @codeCoverageIgnore
     * @param callable $cb A callback that should return a chain of clause calls
     * @param string $logical A logical operator used to concatenate the clause
     * @return \p810\MySQL\Builder\BuilderInterface
     */
    public function orWhereNested(callable $cb): BuilderInterface
    {
        return $this->whereNested($cb, 'or');
    }

    /**
     * Compiles the where clause
     * 
     * @return null|string
     */
    protected function compileWhere(): ?string
    {
        $wheres = $this->getParameter('where');

        if (! $wheres) {
            return null;
        }

        return 'where ' . Expression::listToString($wheres);
    }
}
