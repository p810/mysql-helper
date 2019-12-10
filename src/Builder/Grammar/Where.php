<?php

namespace p810\MySQL\Builder\Grammar;

use p810\MySQL\Builder\BuilderInterface;

use function p810\MySQL\keywordToString;

trait Where
{
    /**
     * @var array<int,\p810\MySQL\Builder\Grammar\Expression|string>
     */
    protected $wheres = [];

    /**
     * Appends an expression to the where clause
     * 
     * @param mixed|\p810\MySQL\Builder\BuilderInterface $column Left hand side of the expression
     * @param mixed|\p810\MySQL\Builder\BuilderInterface $value Right hand side of the expression
     * @param string $operator Middle of the expression (comparison operator)
     * @param string $logical A logical operator used to concatenate the expression in the clause
     * @param bool $bind Determines whether the given value should be bound to the query
     * @return \p810\MySQL\Builder\BuilderInterface
     */
    public function where(
        $column,
        $value,
        string $operator = '=',
        string $logical = 'and',
        bool $bind = true
    ): BuilderInterface {
        $wheres = $this->getParameter('where') ?? [];

        [$column, $value] = $this->prepare([$column, $value]);

        if ($bind) {
            $value = $this->bind($value);
        }

        $wheres[] = new Expression($column, $value, $operator, $logical);

        return $this->setParameter('where', $wheres);
    }

    /**
     * Appends an expression with "or" as the logical operator
     * 
     * @param mixed|\p810\MySQL\Builder\BuilderInterface $column Left hand side of the expression
     * @param mixed|\p810\MySQL\Builder\BuilderInterface $value Right hand side of the expression
     * @param string $operator Middle of the expression (comparison operator)
     * @return \p810\MySQL\Builder\BuilderInterface
     */
    public function orWhere($column, $value, string $operator = '='): BuilderInterface
    {
        return $this->where($column, $value, $operator, 'or');
    }

    /**
     * Appends an expression with "!=" as the comparison operator
     * 
     * @param mixed|\p810\MySQL\Builder\BuilderInterface $column Left hand side of the expression
     * @param mixed|\p810\MySQL\Builder\BuilderInterface $value Right hand side of the expression
     * @param string $logical A logical operator used to concatenate the expression in the clause
     * @return \p810\MySQL\Builder\BuilderInterface
     */
    public function whereNotEquals($column, $value, string $logical = 'and'): BuilderInterface
    {
        return $this->where($column, $value, '!=', $logical);
    }

    /**
     * An alias for `\p810\MySQL\Builder\Grammar\Where::whereNotEquals()`
     * 
     * @param mixed|\p810\MySQL\Builder\BuilderInterface $column Lefthand side of the expression
     * @param mixed|\p810\MySQL\Builder\BuilderInterface $value Righthand side of the expression
     * @param string $logical A logical operator used to concatenate the expression in the clause
     * @return \p810\MySQL\Builder\BuilderInterface
     */
    public function whereNot($column, $value, string $logical = 'and'): BuilderInterface
    {
        return $this->whereNotEquals($column, $value, $logical);
    }

    /**
     * An alias for `\p810\MySQL\Builder\Grammar\Where::whereNotEquals()`, but specifies "or" as the logical operator
     * 
     * @codeCoverageIgnore
     * @param mixed|\p810\MySQL\Builder\BuilderInterface $column Left hand side of the expression
     * @param mixed|\p810\MySQL\Builder\BuilderInterface $value Right hand side of the expression
     * @return \p810\MySQL\Builder\BuilderInterface
     */
    public function orWhereNotEquals($column, $value): BuilderInterface
    {
        return $this->whereNotEquals($column, $value, 'or');
    }

    /**
     * An alias for `\p810\MySQL\Builder\Grammar\Where::orWhereNotEquals()`
     * 
     * @codeCoverageIgnore
     * @param mixed|\p810\MySQL\Builder\BuilderInterface $column Left hand side of the expression
     * @param mixed|\p810\MySQL\Builder\BuilderInterface $value Right hand side of the expression
     * @return \p810\MySQL\Builder\BuilderInterface
     */
    public function orWhereNot($column, $value): BuilderInterface
    {
        return $this->orWhereNotEquals($column, $value);
    }

    /**
     * Appends an expression with "<" as the comparison operator
     * 
     * @param mixed|\p810\MySQL\Builder\BuilderInterface $column Left hand side of the expression
     * @param mixed|\p810\MySQL\Builder\BuilderInterface $value Right hand side of the expression
     * @param string $logical A logical operator used to concatenate the expression in the clause
     * @return \p810\MySQL\Builder\BuilderInterface
     */
    public function whereLess($column, $value, string $logical = 'and'): BuilderInterface
    {
        return $this->where($column, $value, '<', $logical);
    }

    /**
     * An alias for `\p810\MySQL\Builder\Grammar\Where::whereLess()`, but specifies "or" as the logical operator
     * 
     * @param mixed|\p810\MySQL\Builder\BuilderInterface $column Left hand side of the expression
     * @param mixed|\p810\MySQL\Builder\BuilderInterface $value Right hand side of the expression
     * @return \p810\MySQL\Builder\BuilderInterface
     */
    public function orWhereLess($column, $value): BuilderInterface
    {
        return $this->whereLess($column, $value, 'or');
    }

    /**
     * Appends an expression with "<=" as the comparison operator
     * 
     * @param mixed|\p810\MySQL\Builder\BuilderInterface $column Left hand side of the expression
     * @param mixed|\p810\MySQL\Builder\BuilderInterface $value Right hand side of the expression
     * @param string $logical A logical operator used to concatenate the expression in the clause
     * @return \p810\MySQL\Builder\BuilderInterface
     */
    public function whereLessOrEqual($column, $value, string $logical = 'and'): BuilderInterface
    {
        return $this->where($column, $value, '<=', $logical);
    }

    /**
     * An alias for `\p810\MySQL\Builder\Grammar\Where::whereLessOrEqual()`, but specifies "or" as the logical operator
     * 
     * @codeCoverageIgnore
     * @param mixed|\p810\MySQL\Builder\BuilderInterface $column Left hand side of the expression
     * @param mixed|\p810\MySQL\Builder\BuilderInterface $value Right hand side of the expression
     * @return \p810\MySQL\Builder\BuilderInterface
     */
    public function orWhereLessOrEqual($column, $value): BuilderInterface
    {
        return $this->whereLessOrEqual($column, $value, 'or');
    }

    /**
     * Appends an expression with ">" as the comparison operator
     * 
     * @param mixed|\p810\MySQL\Builder\BuilderInterface $column Left hand side of the expression
     * @param mixed|\p810\MySQL\Builder\BuilderInterface $value Right hand side of the expression
     * @param string $logical A logical operator used to concatenate the expression in the clause
     * @return \p810\MySQL\Builder\BuilderInterface
     */
    public function whereGreater($column, $value, string $logical = 'and'): BuilderInterface
    {
        return $this->where($column, $value, '>', $logical);
    }

    /**
     * An alias for `\p810\MySQL\Builder\Grammar\Where::whereGreater()`, but specifies "or" as the logical operator
     * 
     * @codeCoverageIgnore
     * @param mixed|\p810\MySQL\Builder\BuilderInterface $column Left hand side of the expression
     * @param mixed|\p810\MySQL\Builder\BuilderInterface $value Right hand side of the expression
     * @return \p810\MySQL\Builder\BuilderInterface
     */
    public function orWhereGreater($column, $value): BuilderInterface
    {
        return $this->whereGreater($column, $value, 'or');
    }

    /**
     * Appends an expression with ">=" as the comparison operator
     * 
     * @param mixed|\p810\MySQL\Builder\BuilderInterface $column Left hand side of the expression
     * @param mixed|\p810\MySQL\Builder\BuilderInterface $value Right hand side of the expression
     * @param string $logical A logical operator used to concatenate the expression in the clause
     * @return \p810\MySQL\Builder\BuilderInterface
     */
    public function whereGreaterOrEqual($column, $value, string $logical = 'and'): BuilderInterface
    {
        return $this->where($column, $value, '>=', $logical);
    }

    /**
     * An alias for `\p810\MySQL\Builder\Grammar\Where::whereGreaterOrEqual()` that specifies "or" as the logical
     * operator
     * 
     * @codeCoverageIgnore
     * @param mixed|\p810\MySQL\Builder\BuilderInterface $column Left hand side of the expression
     * @param mixed|\p810\MySQL\Builder\BuilderInterface $value Right hand side of the expression
     * @return \p810\MySQL\Builder\BuilderInterface
     */
    public function orWhereGreaterOrEqual($column, $value): BuilderInterface
    {
        return $this->whereGreaterOrEqual($column, $value, 'or');
    }

    /**
     * Appends an expression with "like" as the comparison operator
     * 
     * @param mixed|\p810\MySQL\Builder\BuilderInterface $column Left hand side of the expression
     * @param mixed|\p810\MySQL\Builder\BuilderInterface $value Right hand side of the expression
     * @param string $logical A logical operator used to concatenate the expression in the clause
     * @return \p810\MySQL\Builder\BuilderInterface
     */
    public function whereLike($column, $value, string $logical = 'and'): BuilderInterface
    {
        return $this->where($column, $value, 'like', $logical);
    }

    /**
     * An alias for `\p810\MySQL\Builder\Grammar\Where::whereLike()` that specifies "or" as the logical operator
     * 
     * @codeCoverageIgnore
     * @param mixed|\p810\MySQL\Builder\BuilderInterface $column Left hand side of the expression
     * @param mixed|\p810\MySQL\Builder\BuilderInterface $value Right hand side of the expression
     * @return \p810\MySQL\Builder\BuilderInterface
     */
    public function orWhereLike($column, $value): BuilderInterface
    {
        return $this->whereLike($column, $value, 'or');
    }

    /**
     * Appends an expression with "not like" as the comparison operator
     * 
     * @param mixed|\p810\MySQL\Builder\BuilderInterface $column Left hand side of the expression
     * @param mixed|\p810\MySQL\Builder\BuilderInterface $value Right hand side of the expression
     * @param string $logical A logical operator used to concatenate the expression in the clause
     * @return \p810\MySQL\Builder\BuilderInterface
     */
    public function whereNotLike($column, $value, string $logical = 'and'): BuilderInterface
    {
        return $this->where($column, $value, 'not like', $logical);
    }

    /**
     * An alias for `\p810\MySQL\Builder\Grammar\Where::whereNotLike()` that specifies "or" as the logical operator
     * 
     * @codeCoverageIgnore
     * @param mixed|\p810\MySQL\Builder\BuilderInterface $column Left hand side of the expression
     * @param mixed|\p810\MySQL\Builder\BuilderInterface $value Right hand side of the expression
     * @return \p810\MySQL\Builder\BuilderInterface
     */
    public function orWhereNotLike($column, $value): BuilderInterface
    {
        return $this->where($column, $value, 'not like', 'or');
    }

    /**
     * Appends an expression with "in" as the comparison operator
     * 
     * @param mixed|\p810\MySQL\Builder\BuilderInterface $column Left hand side of the expression
     * @param mixed|\p810\MySQL\Builder\BuilderInterface $value Right hand side of the expression
     * @param string $logical A logical operator used to concatenate the expression in the clause
     * @return \p810\MySQL\Builder\BuilderInterface
     */
    public function whereIn($column, $value, string $logical = 'and'): BuilderInterface
    {
        return $this->where($column, $value, 'in', $logical);
    }

    /**
     * An alias for `\p810\MySQL\Builder\Grammar\Where::whereIn()` that specifies "or" as the logical operator
     * 
     * @codeCoverageIgnore
     * @param mixed|\p810\MySQL\Builder\BuilderInterface $column Left hand side of the expression
     * @param mixed|\p810\MySQL\Builder\BuilderInterface $value Right hand side of the expression
     * @return \p810\MySQL\Builder\BuilderInterface
     */
    public function orWhereIn($column, $value): BuilderInterface
    {
        return $this->whereIn($column, $value, 'or');
    }

    /**
     * Appends an expression with "not in" as the comparison operator
     * 
     * @param mixed|\p810\MySQL\Builder\BuilderInterface $column Left hand side of the expression
     * @param mixed|\p810\MySQL\Builder\BuilderInterface $value Right hand side of the expression
     * @param string $logical A logical operator used to concatenate the expression in the clause
     * @return \p810\MySQL\Builder\BuilderInterface
     */
    public function whereNotIn($column, $value, string $logical = 'and'): BuilderInterface
    {
        return $this->where($column, $value, 'not in', $logical);
    }

    /**
     * An alias for `\p810\MySQL\Builder\Grammar\Where::whereNotIn()` that specifies "or" as the logical operator
     * 
     * @codeCoverageIgnore
     * @param mixed|\p810\MySQL\Builder\BuilderInterface $column Left hand side of the expression
     * @param mixed|\p810\MySQL\Builder\BuilderInterface $value Right hand side of the expression
     * @return \p810\MySQL\Builder\BuilderInterface
     */
    public function orWhereNotIn($column, $value): BuilderInterface
    {
        return $this->whereNotIn($column, $value, 'or');
    }

    /**
     * Appends an expression with "between" as the comparison operator
     * 
     * @param mixed|\p810\MySQL\Builder\BuilderInterface $expression Left hand side of the expression
     * @param mixed|\p810\MySQL\Builder\BuilderInterface $min The min value on the right hand side of the expression
     * @param mixed|\p810\MySQL\Builder\BuilderInterface $max The max value on the right hand side of the expression
     * @param string $logical A logical operator used to concatenate the expression in the clause
     * @return \p810\MySQL\Builder\BuilderInterface
     */
    public function whereBetween($expression, $min, $max, string $logical = 'and'): BuilderInterface
    {
        [$min, $max] = $this->prepare([$min, $max]);

        return $this->where($expression, "$min and $max", 'between', $logical);
    }

    /**
     * An alias for `\p810\MySQL\Builder\Grammar\Where::whereBetween()` that specifies "or" as the logical operator
     * 
     * @codeCoverageIgnore
     * @param mixed|\p810\MySQL\Builder\BuilderInterface $expression Left hand side of the expression
     * @param mixed|\p810\MySQL\Builder\BuilderInterface $min The min value on the right hand side of the expression
     * @param mixed|\p810\MySQL\Builder\BuilderInterface $max The max value on the right hand side of the expression
     * @return \p810\MySQL\Builder\BuilderInterface
     */
    public function orWhereBetween($expression, $min, $max): BuilderInterface
    {
        return $this->whereBetween($expression, $min, $max, 'or');
    }

    /**
     * Appends an expression with "not between" as the comparison operator
     * 
     * @param mixed|\p810\MySQL\Builder\BuilderInterface $expression Left hand side of the expression
     * @param mixed|\p810\MySQL\Builder\BuilderInterface $min The min value on the right hand side of the expression
     * @param mixed|\p810\MySQL\Builder\BuilderInterface $max The max value on the right hand side of the expression
     * @param string $logical A logical operator used to concatenate the expression in the clause
     * @return \p810\MySQL\Builder\BuilderInterface
     */
    public function whereNotBetween($expression, $min, $max, string $logical = 'and'): BuilderInterface
    {
        [$min, $max] = $this->prepare([$min, $max]);

        return $this->where($expression, "$min and $max", 'not between', $logical);
    }

    /**
     * An alias for `\p810\MySQL\Builder\Grammar\Where::whereBetween()` that specifies "or" as the logical operator
     * 
     * @codeCoverageIgnore
     * @param mixed|\p810\MySQL\Builder\BuilderInterface $expression Left hand side of the expression
     * @param mixed|\p810\MySQL\Builder\BuilderInterface $min The min value on the right hand side of the expression
     * @param mixed|\p810\MySQL\Builder\BuilderInterface $max The max value on the right hand side of the expression
     * @return \p810\MySQL\Builder\BuilderInterface
     */
    public function orWhereNotBetween($expression, $min, $max): BuilderInterface
    {
        return $this->whereNotBetween($expression, $min, $max, 'or');
    }

    /**
     * Appends an expression with "is" as the comparison operator
     * 
     * If null is given as the clause's boolean it will be interpreted to mean `UNKNOWN`; to specify null in the query,
     * use `\p810\MySQL\Builder\Grammar\Where::whereIsNull()`
     * 
     * @param mixed|\p810\MySQL\Builder\BuilderInterface $value Left hand side of the expression
     * @param null|bool|string $boolean The clause's boolean
     * @param string $logical A logical operator used to concatenate the expression in the clause
     * @return \p810\MySQL\Builder\BuilderInterface
     */
    public function whereIs($value, $boolean = 'true', string $logical = 'and'): BuilderInterface
    {
        return $this->where($this->bind($value), keywordToString($boolean, true), 'is', $logical, false);
    }

    /**
     * An alias for `\p810\MySQL\Builder\Grammar\Where::whereIs()` that specifies "or" as the logical operator
     * 
     * @param mixed|\p810\MySQL\Builder\BuilderInterface $value Left hand side of the expression
     * @param null|bool|string $boolean The clause's boolean
     * @return \p810\MySQL\Builder\BuilderInterface
     */
    public function orWhereIs($value, $boolean = 'true'): BuilderInterface
    {
        return $this->whereIs($value, $boolean, 'or');
    }

    /**
     * An alias for `\p810\MySQL\Builder\Grammar\Where::whereIs()` that specifies "false" as the boolean
     * 
     * @param mixed|\p810\MySQL\Builder\BuilderInterface $value Left hand side of the expression
     * @param string $logical A logical operator used to concatenate the expression in the clause
     * @return \p810\MySQL\Builder\BuilderInterface
     */
    public function whereIsFalse($value, string $logical = 'and'): BuilderInterface
    {
        return $this->whereIs($value, 'false', $logical);
    }

    /**
     * An alias for `\p810\MySQL\Builder\Grammar\Where::whereIs()` that specifies "false" as the boolean and "or" as
     * the logical operator
     * 
     * @param mixed|\p810\MySQL\Builder\BuilderInterface $value Left hand side of the expression
     * @return \p810\MySQL\Builder\BuilderInterface
     */
    public function orWhereIsFalse($value): BuilderInterface
    {
        return $this->whereIsFalse($value, 'or');
    }

    /**
     * An alias for `\p810\MySQL\Builder\Grammar\Where::whereIs()` that specifies "unknown" as the boolean
     * 
     * @param mixed|\p810\MySQL\Builder\BuilderInterface $value Left hand side of the expression
     * @param string $logical A logical operator used to concatenate the expression in the clause
     * @return \p810\MySQL\Builder\BuilderInterface
     */
    public function whereIsUnknown($value, string $logical = 'and'): BuilderInterface
    {
        return $this->whereIs($value, 'unknown', $logical);
    }

    /**
     * An alias for `\p810\MySQL\Builder\Grammar\Where::whereIs()` that specifies "unknown" as the boolean and "or" as
     * the logical operator
     * 
     * @param mixed|\p810\MySQL\Builder\BuilderInterface $value Left hand side of the expression
     * @return \p810\MySQL\Builder\BuilderInterface
     */
    public function orWhereIsUnknown($value): BuilderInterface
    {
        return $this->whereIsUnknown($value, 'or');
    }

    /**
     * Appends an expression with "is not" as the comparison operator
     *
     * If null is given as the clause's boolean it will be interpreted to mean `UNKNOWN`; to specify null in the query,
     * use `\p810\MySQL\Builder\Grammar\Where::whereIsNotNull()`
     * 
     * @param mixed|\p810\MySQL\Builder\BuilderInterface $value Left hand side of the expression
     * @param null|bool|string $boolean The clause's boolean
     * @param string $logical A logical operator used to concatenate the expression in the clause
     * @return \p810\MySQL\Builder\BuilderInterface
     */
    public function whereIsNot($value, $boolean = 'true', string $logical = 'and'): BuilderInterface
    {
        return $this->where($this->bind($value), keywordToString($boolean, true), 'is not', $logical, false);
    }

    /**
     * An alias for `\p810\MySQL\Builder\Grammar\Where::whereIsNot()` that specifies "or" as the logical operator
     * 
     * @param mixed|\p810\MySQL\Builder\BuilderInterface $value Left hand side of the expression
     * @param null|bool|string $boolean The clause's boolean
     * @return \p810\MySQL\Builder\BuilderInterface
     */
    public function orWhereIsNot($value, $boolean = 'true'): BuilderInterface
    {
        return $this->whereIsNot($value, $boolean, 'or');
    }

    /**
     * An alias for `\p810\MySQL\Builder\Grammar\Where::whereIsNot()` that specifies "false" as the boolean
     * 
     * @param mixed|\p810\MySQL\Builder\BuilderInterface $value Left hand side of the expression
     * @param string $logical A logical operator used to concatenate the expression in the clause
     * @return \p810\MySQL\Builder\BuilderInterface
     */
    public function whereIsNotFalse($value, string $logical = 'and'): BuilderInterface
    {
        return $this->whereIsNot($value, 'false', $logical);
    }

    /**
     * An alias for `\p810\MySQL\Builder\Grammar\Where::whereIsNot()` that specifies "false" as the boolean and "or" as
     * the logical operator
     * 
     * @param mixed|\p810\MySQL\Builder\BuilderInterface $value Left hand side of the expression
     * @return \p810\MySQL\Builder\BuilderInterface
     */
    public function orWhereIsNotFalse($value): BuilderInterface
    {
        return $this->whereIsNotFalse($value, 'or');
    }

    /**
     * An alias for `\p810\MySQL\Builder\Grammar\Where::whereIsNot()` that specifies "unknown" as the boolean
     * 
     * @param mixed|\p810\MySQL\Builder\BuilderInterface $value Left hand side of the expression
     * @param string $logical A logical operator used to concatenate the expression in the clause
     * @return \p810\MySQL\Builder\BuilderInterface
     */
    public function whereIsNotUnknown($value, string $logical = 'and'): BuilderInterface
    {
        return $this->whereIsNot($value, 'unknown', $logical);
    }

    /**
     * An alias for `\p810\MySQL\Builder\Grammar\Where::whereIsNot()` that specifies "unknown" as the boolean and "or"
     * as the logical operator
     * 
     * @param mixed|\p810\MySQL\Builder\BuilderInterface $value Left hand side of the expression
     * @return \p810\MySQL\Builder\BuilderInterface
     */
    public function orWhereIsNotUnknown($value): BuilderInterface
    {
        return $this->whereIsNotUnknown($value, 'or');
    }

    /**
     * Appends an expression with "is null" as the comparison operator
     * 
     * @param mixed|\p810\MySQL\Builder\BuilderInterface $value Left hand side of the expression
     * @param string $logical A logical operator used to concatenate the expression in the clause
     * @return \p810\MySQL\Builder\BuilderInterface
     */
    public function whereIsNull($value, string $logical = 'and'): BuilderInterface
    {
        return $this->whereIs($this->bind($value), 'null', $logical);
    }

    /**
     * An alias for `\p810\MySQL\Builder\Grammar\Where::whereIsNull()` that specifies "or" as the logical operator
     * 
     * @codeCoverageIgnore
     * @param mixed|\p810\MySQL\Builder\BuilderInterface $value Left hand side of the expression
     * @return \p810\MySQL\Builder\BuilderInterface
     */
    public function orWhereIsNull($value): BuilderInterface
    {
        return $this->whereIsNull($value, 'or');
    }

    /**
     * Appends an expression with "is not null" as the comparison operator
     * 
     * @param mixed|\p810\MySQL\Builder\BuilderInterface $value Left hand side of the expression
     * @param string $logical A logical operator used to concatenate the expression in the clause
     * @return \p810\MySQL\Builder\BuilderInterface
     */
    public function whereIsNotNull($value, string $logical = 'and'): BuilderInterface
    {
        return $this->whereIsNot($value, 'null', $logical);
    }

    /**
     * An alias for `\p810\MySQL\Builder\Grammar\Where::whereIsNotNull()` that specifies "or" as the logical operator
     * 
     * @codeCoverageIgnore
     * @param mixed|\p810\MySQL\Builder\BuilderInterface $value Left hand side of the expression
     * @return \p810\MySQL\Builder\BuilderInterface
     */
    public function orWhereIsNotNull($value): BuilderInterface
    {
        return $this->whereIsNotNull($value, 'or');
    }

    /**
     * Appends an expression with "<=>" as the comparison operator
     * 
     * @param mixed|\p810\MySQL\Builder\BuilderInterface $column Left hand side of the expression
     * @param mixed|\p810\MySQL\Builder\BuilderInterface $value Right hand side of the expression
     * @param string $logical A logical operator used to concatenate the expression in the clause
     * @return \p810\MySQL\Builder\BuilderInterface
     */
    public function whereNullSafe($column, $value, string $logical = 'and'): BuilderInterface
    {
        return $this->where($column, $value, '<=>', $logical);
    }

    /**
     * An alias for `\p810\MySQL\Builder\Grammar\Where::whereNullSafe()` that specifies "or" as the logical operator
     * 
     * @codeCoverageIgnore
     * @param mixed|\p810\MySQL\Builder\BuilderInterface $column Left hand side of the expression
     * @param mixed|\p810\MySQL\Builder\BuilderInterface $value Right hand side of the expression
     * @return \p810\MySQL\Builder\BuilderInterface
     */
    public function orWhereNullSafe($column, $value): BuilderInterface
    {
        return $this->whereNullSafe($column, $value, 'or');
    }

    /**
     * An alias for `\p810\MySQL\Builder\Grammar\Where::whereNullSafe()`
     * 
     * @codeCoverageIgnore
     * @param mixed|\p810\MySQL\Builder\BuilderInterface $column Left hand side of the expression
     * @param mixed|\p810\MySQL\Builder\BuilderInterface $value Right hand side of the expression
     * @param string $logical A logical operator used to concatenate the expression in the clause
     * @return \p810\MySQL\Builder\BuilderInterface
     */
    public function whereNullSafeEquals($column, $value, string $logical = 'and'): BuilderInterface
    {
        return $this->whereNullSafe($column, $value, $logical);
    }

    /**
     * An alias for `\p810\MySQL\Builder\Grammar\Where::orWhereNullSafe()`
     * 
     * @codeCoverageIgnore
     * @param mixed|\p810\MySQL\Builder\BuilderInterface $column Left hand side of the expression
     * @param mixed|\p810\MySQL\Builder\BuilderInterface $value Right hand side of the expression
     * @param string $logical A logical operator used to concatenate the expression in the clause
     * @return \p810\MySQL\Builder\BuilderInterface
     */
    public function orWhereNullSafeEquals($column, $value): BuilderInterface
    {
        return $this->orWhereNullSafe($column, $value);
    }

    /**
     * Appends a raw where clause to the list of expressions
     * 
     * @param string $clause The clause to append
     * @param string $logical A logical operator used to concatenate this clause to one before it
     * @return \p810\MySQL\Builder\BuilderInterface
     */
    public function whereRaw(string $clause, string $logical = 'and'): BuilderInterface
    {
        $wheres = $this->getParameter('where') ?? [];

        if ($wheres) {
            $clause = "$logical $clause";
        }

        $wheres[] = $clause;

        return $this->setParameter('where', $wheres);
    }

    /**
     * An alias for `\p810\MySQL\Builder\Grammar\Where::whereRaw()` that specifies "or" as the logical operator
     *
     * @codeCoverageIgnore
     * @param string $clause The clause to append
     * @return \p810\MySQL\Builder\BuilderInterface
     */
    public function orWhereRaw(string $clause): BuilderInterface
    {
        return $this->whereRaw($clause, 'or');
    }

    /**
     * Appends a nested where clause
     * 
     * The callback that this method receives must take an instance of `p810\MySQL\Builder\Grammar\ComplexWhere` as its
     * first argument and return that object. An example of how that callback might look is:
     *
     * ```php
     * $query->whereNested(function (BuilderInterface $q): BuilderInterface {
     *     return $q->where('foo', 'bar')->orWhere('bam', 'baz');
     * });
     * ```
     * 
     * This would generate the following: `(foo = ? or bar = ?)`
     * 
     * @param callable $cb A callback that should return a chain of clause calls
     * @param string $logical A logical operator used to concatenate the clause
     * @return \p810\MySQL\Builder\BuilderInterface
     * @psalm-suppress PossiblyInvalidArgument
     */
    public function whereNested(callable $cb, string $logical = 'and'): BuilderInterface
    {
        $query = $cb(new ComplexWhere());

        return $this->whereRaw($this->prepare($query), $logical);
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
