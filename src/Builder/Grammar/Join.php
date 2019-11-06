<?php

namespace p810\MySQL\Builder\Grammar;

use function p810\MySQL\spaces;

trait Join
{
    /**
     * @var \p810\MySQL\Builder\Grammar\JoinExpression[]
     */
    protected $joins;

    /**
     * @var null|\p810\MySQL\Builder\Grammar\JoinExpression
     */
    protected $currentJoin;

    /**
     * @var array
     */
    protected $callsBeforeFirstJoin = [];

    /**
     * Appends a join to the query
     * 
     * @param string $type The type of join (e.g. inner, left)
     * @param string $table The table to join data from 
     * @return self
     */
    protected function join(string $type, string $table): self
    {
        $join = new JoinExpression($type, $table);

        if ($this->callsBeforeFirstJoin) {
            // if the $callsBeforeFirstJoin has items, that means that either Join::on()
            // or Join::using() was called before this method was, so we need to unpack
            // all of that data into the current JoinExpression
            foreach ($this->callsBeforeFirstJoin as $method => $calls) {
                foreach ($calls as $arguments) {
                    $join->$method(...$arguments);
                }
            }

            $this->callsBeforeFirstJoin = [];
        }

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
     */
    public function using(string $column): self
    {
        if (! $this->currentJoin instanceof JoinExpression) {        
            $this->callsBeforeFirstJoin['using'][] = [$column];
        } else {
            $this->currentJoin->using($column);
        }

        return $this;
    }

    /**
     * Appends an "on" clause to the current join
     * 
     * @param string $left The left hand column
     * @param string $right The right hand column
     * @param string $logical A logical operator used to concatenate the clauses
     * @return self
     */
    public function on(string $left, string $right, string $logical = 'and'): self
    {
        if (! $this->currentJoin instanceof JoinExpression) {
            $this->callsBeforeFirstJoin['on'][] = [$left, $right, $logical];
        } else {
            $this->currentJoin->on($left, $right, $logical);
        }

        return $this;
    }

    /**
     * Appends an "on" clause to the current join with "or" as the logical operator
     * 
     * @param string $left The left hand column
     * @param string $right The right hand column
     * @return self
     */
    public function orOn(string $left, string $right): self
    {
        return $this->on($left, $right, 'or');
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
