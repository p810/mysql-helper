<?php

namespace p810\MySQL\Builder\Grammar;

use p810\MySQL\Query;
use InvalidArgumentException;
use p810\MySQL\Builder\Token;

trait Where
{
    /**
     * @var \p810\MySQL\Builder\Grammar\Expression[]
     */
    protected $wheres;

    /**
     * @param mixed $value
     */
    public function where(string $column, $value, string $operator = '=', string $logical = 'and'): self
    {
        $this->wheres[] = new Expression($column, $this->bind($value), $operator, $logical);

        return $this;
    }

    /**
     * @param mixed $value
     */
    public function whereOr(string $column, $value, string $operator = '='): self
    {
        return $this->where($column, $value, $operator, 'or');
    }

    /**
     * @param mixed $value
     */
    public function whereNotEquals(string $column, $value, string $logical = 'and'): self
    {
        return $this->where($column, $value, '!=', $logical);
    }

    /**
     * @param mixed $value
     */
    public function whereLess(string $column, $value, string $logical = 'and'): self
    {
        return $this->where($column, $value, '<', $logical);
    }

    /**
     * @param mixed $value
     */
    public function whereLessOrEqual(string $column, $value, string $logical = 'and'): self
    {
        return $this->where($column, $value, '<=', $logical);
    }

    /**
     * @param mixed $value
     */
    public function whereGreater(string $column, $value, string $logical = 'and'): self
    {
        return $this->where($column, $value, '>', $logical);
    }

    /**
     * @param mixed $value
     */
    public function whereGreaterOrEqual(string $column, $value, string $logical = 'and'): self
    {
        return $this->where($column, $value, '>=', $logical);
    }

    /**
     * @param mixed $value
     */
    public function whereLike(string $column, $value, string $logical = 'and'): self
    {
        return $this->where($column, $value, 'like', $logical);
    }

    /**
     * @param \p810\MySQL\Query|array $value
     * @throws \InvalidArgumentException
     */
    public function whereIn(string $columnOrExpression, $value, string $logical = 'and'): self
    {
        return $this->where($columnOrExpression, $value, 'in', $logical);
    }

    /**
     * @param \p810\MySQL\Query|array $value
     * @throws \InvalidArgumentException
     */
    public function whereNotIn(string $columnOrExpression, $value, string $logical = 'and'): self
    {
        return $this->where($columnOrExpression, $value, 'not in', $logical);
    }

    protected function compileWhere(): ?string
    {
        if (! $this->wheres) {
            return null;
        }

        return 'where ' . Expression::listToString($this->wheres);
    }
}