<?php

namespace p810\MySQL\Builder\Grammar;

use p810\MySQL\Exception\MissingArgumentException;

use function p810\MySQL\spaces;

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

    /**
     * Appends a join to the query
     * 
     * @param string $type  The type of join (e.g. inner, left)
     * @param string $table The table to join data from 
     * @return self
     */
    protected function join(string $type, string $table): self
    {
        $join = new JoinExpression($type, $table);

        $this->joins[] = $join;
        $this->currentJoin = $join;

        return $this;
    }

    /**
     * Appends an inner join to the query
     * 
     * @param string $table The table to join data from
     * @return self
     */
    public function innerJoin(string $table): self
    {
        return $this->join('inner', $table);
    }

    /**
     * Appends a left join to the query
     * 
     * @param string $table The table to join data from
     * @return self
     */
    public function leftJoin(string $table): self
    {
        return $this->join('left', $table);
    }

    /**
     * Appends a right join to the query
     * 
     * @param string $table The table to join data from
     * @return self
     */
    public function rightJoin(string $table): self
    {
        return $this->join('right', $table);
    }

    /**
     * Appends a left outer join to the query
     * 
     * @param string $table The table to join data from
     * @return self
     */
    public function leftOuterJoin(string $table): self
    {
        return $this->join('left outer', $table);
    }

    /**
     * Appends a right outer join to the query
     * 
     * @param string $table The table to join data from
     * @return self
     */
    public function rightOuterJoin(string $table): self
    {
        return $this->join('right outer', $table);
    }

    /**
     * Appends a "using" clause to the current join
     * 
     * @param string $column The column that both tables have in common
     * @return self
     * @throws \p810\MySQL\Exception\MissingArgumentException if this method is called before a join is set
     */
    public function using(string $column): self
    {
        if (! $this->currentJoin instanceof JoinExpression) {
            throw new MissingArgumentException('\p810\MySQL\Builder\Grammar\Join::using() cannot be called before a JoinExpression is set');
        }
        
        $this->currentJoin->using($column);

        return $this;
    }

    /**
     * Appends an "on" clause to the current join
     * 
     * @param string $left    The lefthand column
     * @param string $right   The righthand column
     * @param string $logical A logical operator used to concatenate the clauses
     * @return self
     * @throws \p810\MySQL\Exception\MissingArgumentException if this method is called before a join is set
     */
    public function on(string $left, string $right, string $logical = 'and'): self
    {
        if (! $this->currentJoin instanceof JoinExpression) {
            throw new MissingArgumentException('\p810\MySQL\Builder\Grammar\Join::on() cannot be called before a JoinExpression is set');
        }

        $this->currentJoin->on($left, $right, $logical);

        return $this;
    }

    /**
     * Compiles the join clause
     * 
     * @return null|string
     */
    protected function compileJoin(): ?string
    {
        if (! $this->joins) {
            return null;
        }

        return spaces($this->joins);
    }
}