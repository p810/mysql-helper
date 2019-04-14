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

    /** @todo: Place other methods like rightJoin() here */

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