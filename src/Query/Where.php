<?php

namespace p810\MySQL\Query;

use p810\MySQL\Query;
use InvalidArgumentException;

use function count;
use function sprintf;
use function is_array;
use function array_map;

trait Where
{
    /**
     * @var \p810\MySQL\Query\Clause[]
     */
    protected $clauses;

    /**
     * @var string|null
     */
    protected $nextLogicalOperator;

    /**
     * @param mixed $value
     */
    public function where(string $column, $value, string $operator = '=', string $logical = 'AND'): self
    {
        // if this property is set then the user called and(),
        // or(), or between(), to indicate that the following
        // method call on their query should be joined with that
        // logical operator
        if ($this->nextLogicalOperator) {
            $logical = $this->nextLogicalOperator;
            $this->nextLogicalOperator = null;
        }

        $this->clauses[] = new Clause($column, $this->bind($value), $operator, $logical);

        return $this;
    }

    public function and(): self
    {
        $this->nextLogicalOperator = 'AND';

        return $this;
    }

    public function or(): self
    {
        $this->nextLogicalOperator = 'OR';

        return $this;
    }

    public function between(): self
    {
        $this->nextLogicalOperator = 'BETWEEN';

        return $this;
    }

    /**
     * @param mixed $value
     */
    public function whereOr(string $column, $value, string $operator = '='): self
    {
        return $this->where($column, $value, $operator, 'OR');
    }

    /**
     * @param mixed $value
     */
    public function whereNotEquals(string $column, $value, string $logical = 'AND'): self
    {
        return $this->where($column, $value, '!=', $logical);
    }

    /**
     * @param mixed $value
     */
    public function whereLess(string $column, $value, string $logical = 'AND'): self
    {
        return $this->where($column, $value, '<', $logical);
    }

    /**
     * @param mixed $value
     */
    public function whereLessOrEqual(string $column, $value, string $logical = 'AND'): self
    {
        return $this->where($column, $value, '<=', $logical);
    }

    /**
     * @param mixed $value
     */
    public function whereGreater(string $column, $value, string $logical = 'AND'): self
    {
        return $this->where($column, $value, '>', $logical);
    }

    /**
     * @param mixed $value
     */
    public function whereGreaterOrEqual(string $column, $value, string $logical = 'AND'): self
    {
        return $this->where($column, $value, '>=', $logical);
    }

    /**
     * @param mixed $value
     */
    public function whereLike(string $column, $value, string $logical = 'AND'): self
    {
        return $this->where($column, $value, 'LIKE', $logical);
    }

    /**
     * @param \p810\MySQL\Query|array $value
     * @throws \InvalidArgumentException
     */
    public function whereIn(string $columnOrExpression, $value, string $logical = 'AND'): self
    {
        return $this->where($columnOrExpression, $value, 'IN', $logical);
    }

    /**
     * @param \p810\MySQL\Query|array $value
     * @throws \InvalidArgumentException
     */
    public function whereNotIn(string $columnOrExpression, $value, string $logical = 'AND'): self
    {
        return $this->where($columnOrExpression, $value, 'NOT IN', $logical);
    }

    public function compileWhere(): string
    {
        $clauses = '';
        $count   = count($this->clauses);
        $lastKey = $count - 1;

        for ($i = 0; $i < $count; $i++) {
            $clauses .= ($this->clauses[$i])->compile();

            if ($i < $lastKey) {
                $clauses .= sprintf(' %s ', ($this->clauses[$i + 1])->logicalOperator);
            }
        }

        return "WHERE $clauses";
    }
}