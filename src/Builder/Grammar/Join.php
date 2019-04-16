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

    protected function join(string $type, string $table): self
    {
        $join = new JoinExpression($type, $table);

        $this->joins[] = $join;
        $this->currentJoin = $join;

        return $this;
    }

    public function innerJoin(string $table): self
    {
        return $this->join('inner', $table);
    }

    public function leftJoin(string $table): self
    {
        return $this->join('left', $table);
    }

    public function rightJoin(string $table): self
    {
        return $this->join('right', $table);
    }

    public function leftOuterJoin(string $table): self
    {
        return $this->join('left outer', $table);
    }

    public function rightOuterJoin(string $table): self
    {
        return $this->join('right outer', $table);
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