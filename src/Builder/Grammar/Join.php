<?php

namespace p810\MySQL\Builder\Grammar;

trait Join
{
    /**
     * @var \p810\MySQL\Builder\Grammar\JoinExpression[]
     */
    protected $joins;

    /**
     * @var \p810\MySQL\Builder\Grammar\JoinExpression
     */
    protected $currentJoin;

    public function innerJoin(string $table): self
    {
        $join = new JoinExpression('inner', $table);

        $this->joins[] = $join;
        $this->currentJoin = $join;

        return $this;
    }

    public function leftJoin(string $table): self
    {
        $join = new JoinExpression('left', $table);

        $this->joins[] = $join;
        $this->currentJoin = $join;

        return $this;
    }

    public function rightJoin(string $table): self
    {
        $join = new JoinExpression('right', $table);

        $this->joins[] = $join;
        $this->currentJoin = $join;

        return $this;
    }

    public function leftOuterJoin(string $table): self
    {
        $join = new JoinExpression('left outer', $table);

        $this->joins[] = $join;
        $this->currentJoin = $join;

        return $this;
    }

    public function rightOuterJoin(string $table): self
    {
        $join = new JoinExpression('right outer', $table);

        $this->join[] = $join;
        $this->currentJoin = $join;

        return $this;
    }

    public function using(string $column): self
    {
        $this->currentJoin->using($column);

        return $this;
    }

    public function on(string $left, string $right, string $logical = 'and'): self
    {
        $this->currentJoin->on($left, $right, $logical);

        return $this;
    }

    protected function compileJoin(): ?string
    {
        if (! $this->joins) {
            return null;
        }

        return implode(' ', $this->joins);
    }
}