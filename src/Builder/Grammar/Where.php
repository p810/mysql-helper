<?php

namespace p810\MySQL\Builder\Grammar;

use p810\MySQL\Query;

use function p810\MySQL\parentheses;

trait Where
{
    /**
     * @var array
     */
    protected $wheres;

    /**
     * Appends an expression to the where clause
     * 
     * @param string $column   Lefthand side of the expression (column)
     * @param mixed  $value    Righthand side of the expression (value)
     * @param string $operator Middle of the expression (comparison operator)
     * @param string $logical  A logical operator used to concatenate the expression in the clause
     * @return self
     */
    public function where(string $column, $value, string $operator = '=', string $logical = 'and'): self
    {
        $this->wheres[] = new Expression($column, $this->prepareValue($value), $operator, $logical);

        return $this;
    }

    /**
     * Prepares a value for compilation
     * 
     * If the provided value was an instance of \p810\MySQL\Query, we treat
     * it as a subquery and compile it. Otherwise if it's scalar we bind it
     * and return a placeholder.
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
     * @param string $column   Lefthand side of the expression (column)
     * @param mixed  $value    Righthand side of the expression (value)
     * @param string $operator Middle of the expression (comparison operator)
     * @return self
     */
    public function orWhere(string $column, $value, string $operator = '='): self
    {
        return $this->where($column, $value, $operator, 'or');
    }

    /**
     * Appends an expression with "!=" as the comparison operator
     * 
     * @param string $column  Lefthand side of the expression (column)
     * @param mixed  $value   Righthand side of the expression (value)
     * @param string $logical A logical operator used to concatenate the expression in the clause
     * @return self
     */
    public function whereNotEquals(string $column, $value, string $logical = 'and'): self
    {
        return $this->where($column, $value, '!=', $logical);
    }

    /**
     * An alias for \p810\MySQL\Builder\Grammar\Where::whereNotEquals()
     * 
     * @param string $column  Lefthand side of the expression (column)
     * @param mixed  $value   Righthand side of the expression (value)
     * @param string $logical A logical operator used to concatenate the expression in the clause
     * @return self
     */
    public function whereNot(string $column, $value, string $logical = 'and'): self
    {
        return $this->whereNotEquals($column, $value, $logical);
    }

    /**
     * Appends an expression with "<" as the comparison operator
     * 
     * @param string $column  Lefthand side of the expression (column)
     * @param mixed  $value   Righthand side of the expression (value)
     * @param string $logical A logical operator used to concatenate the expression in the clause
     * @return self
     */
    public function whereLess(string $column, $value, string $logical = 'and'): self
    {
        return $this->where($column, $value, '<', $logical);
    }

    /**
     * Appends an expression with "<=" as the comparison operator
     * 
     * @param string $column  Lefthand side of the expression (column)
     * @param mixed  $value   Righthand side of the expression (value)
     * @param string $logical A logical operator used to concatenate the expression in the clause
     * @return self
     */
    public function whereLessOrEqual(string $column, $value, string $logical = 'and'): self
    {
        return $this->where($column, $value, '<=', $logical);
    }

    /**
     * Appends an expression with ">" as the comparison operator
     * 
     * @param string $column  Lefthand side of the expression (column)
     * @param mixed  $value   Righthand side of the expression (value)
     * @param string $logical A logical operator used to concatenate the expression in the clause
     * @return self
     */
    public function whereGreater(string $column, $value, string $logical = 'and'): self
    {
        return $this->where($column, $value, '>', $logical);
    }

    /**
     * Appends an expression with ">=" as the comparison operator
     * 
     * @param string $column  Lefthand side of the expression (column)
     * @param mixed  $value   Righthand side of the expression (value)
     * @param string $logical A logical operator used to concatenate the expression in the clause
     * @return self
     */
    public function whereGreaterOrEqual(string $column, $value, string $logical = 'and'): self
    {
        return $this->where($column, $value, '>=', $logical);
    }

    /**
     * Appends an expression with "like" as the comparison operator
     * 
     * @param string $column  Lefthand side of the expression (column)
     * @param mixed  $value   Righthand side of the expression (value)
     * @param string $logical A logical operator used to concatenate the expression in the clause
     * @return self
     */
    public function whereLike(string $column, $value, string $logical = 'and'): self
    {
        return $this->where($column, $value, 'like', $logical);
    }

    /**
     * Appends an expression with "in" as the comparison operator
     * 
     * @param string $columnOrExpression     Lefthand side of the expression (column or an expression)
     * @param \p810\MySQL\Query|array $value Righthand side of the expression (a list of scalar values or subquery)
     * @param string $logical                A logical operator used to concatenate the expression in the clause
     * @return self
     */
    public function whereIn(string $columnOrExpression, $value, string $logical = 'and'): self
    {
        return $this->where($columnOrExpression, $value, 'in', $logical);
    }

    /**
     * Appends an expression with "not in" as the comparison operator
     * 
     * @param string $columnOrExpression     Lefthand side of the expression (column or an expression)
     * @param \p810\MySQL\Query|array $value Righthand side of the expression (a list of scalar values or subquery)
     * @param string $logical                A logical operator used to concatenate the expression in the clause
     * @return self
     */
    public function whereNotIn(string $columnOrExpression, $value, string $logical = 'and'): self
    {
        return $this->where($columnOrExpression, $value, 'not in', $logical);
    }

    /**
     * Appends a raw where clause to the list of expressions
     * 
     * Extra care should be taken to make sure that a raw clause doesn't break
     * the flow when the query is compiled
     * 
     * @param string $clause The clause to append
     * @return self
     */
    public function whereRaw(string $clause): self
    {
        $this->wheres[] = $clause;

        return $this;
    }

    /**
     * Appends a nested where clause
     * 
     * The callback that this method receives must take an instance of
     * p810\MySQL\Builder\Grammar\ComplexWhere as its first argument and
     * return that object. An example of how that callback might look is:
     * 
     * $query->whereNested(function (Builder $q) {
     *     return $q->where('foo', 'bar')->orWhere('bam', 'baz');
     * });
     * 
     * This would generate the following:
     * 
     * (foo = ? or bar = ?)
     * 
     * @param callable $cb      A callback that should return a chain of clause calls
     * @param string   $logical A logical operator used to concatenate the clause
     * @return self
     */
    public function whereNested(callable $cb, string $logical = 'and'): self
    {
        $query = $cb(new ComplexWhere);

        $clause = $this->wheres ? "$logical $query" : "$query";

        return $this->whereRaw($clause);
    }

    /**
     * Appends a nested clause with "or" as the logical operator
     * 
     * @param callable $cb      A callback that should return a chain of clause calls
     * @param string   $logical A logical operator used to concatenate the clause
     * @return self
     */
    public function orWhereNested(callable $cb): self
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
        if (! $this->wheres) {
            return null;
        }

        return 'where ' . Expression::listToString($this->wheres);
    }
}