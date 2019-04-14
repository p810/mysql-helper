<?php

namespace p810\MySQL\Builder\Grammar;

use p810\MySQL\Query;
use InvalidArgumentException;
use p810\MySQL\Builder\Token;

use function key;
use function next;
use function count;
use function sprintf;
use function is_array;
use function array_map;
use function array_reduce;

trait Where
{
    /**
     * @var \p810\MySQL\Builder\Grammar\Clause[]
     */
    protected $clauses;

    /**
     * @var string|null
     */
    protected $nextLogicalOperator;

    /**
     * @param mixed $value
     */
    public function where(string $column, $value, string $operator = '=', string $logical = 'and'): self
    {
        if ($this->nextLogicalOperator) {
            $logical = $this->nextLogicalOperator;
            $this->nextLogicalOperator = null;
        }

        $this->clauses[] = new Clause($column, $this->bind($value), $operator, $logical);

        return $this->append(Token::WHERE, $this->getWhereClause());
    }

    public function and(): self
    {
        $this->nextLogicalOperator = 'and';

        return $this;
    }

    public function or(): self
    {
        $this->nextLogicalOperator = 'or';

        return $this;
    }

    public function between(): self
    {
        $this->nextLogicalOperator = 'between';

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

    protected function getWhereClause(): string
    {
        $total = count($this->clauses);

        return array_reduce($this->clauses, function ($value, $clause) use ($total) {
            $value .= $clause->compile();

            if (key($this->clauses) < $total - 1) {
                $next = next($this->clauses);
                $value .= sprintf(' %s ', $next->logicalOperator);
            }

            return $value;
        }, '');
    }
}