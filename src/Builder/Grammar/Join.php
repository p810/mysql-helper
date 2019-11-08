<?php

namespace p810\MySQL\Builder\Grammar;

use BadMethodCallException;
use p810\MySQL\Builder\BuilderInterface;

use function p810\MySQL\spaces;

trait Join
{
    /**
     * @var null|\p810\MySQL\Builder\Grammar\JoinExpression
     */
    private $current;

    /**
     * Appends a join to the query
     * 
     * @param string $type The type of join (e.g. inner, left)
     * @param string $table The table to join data from 
     * @return \p810\MySQL\Builder\Grammar\JoinExpression
     */
    protected function join(string $type, string $table): JoinExpression
    {
        $joins = $this->getParameter('joins') ?? [];

        $this->current = $joins[] = $expression = new JoinExpression($type, $table, $this);

        $this->setParameter('joins', $joins);

        return $expression;
    }

    /**
     * Appends an inner join to the query
     * 
     * @param string $table The table to join data from
     * @return \p810\MySQL\Builder\Grammar\JoinExpression
     */
    public function innerJoin(string $table): JoinExpression
    {
        return $this->join('inner', $table);
    }

    /**
     * Appends a left join to the query
     * 
     * @param string $table The table to join data from
     * @return \p810\MySQL\Builder\Grammar\JoinExpression
     */
    public function leftJoin(string $table): JoinExpression
    {
        return $this->join('left', $table);
    }

    /**
     * Appends a right join to the query
     * 
     * @param string $table The table to join data from
     * @return \p810\MySQL\Builder\Grammar\JoinExpression
     */
    public function rightJoin(string $table): JoinExpression
    {
        return $this->join('right', $table);
    }

    /**
     * Appends a left outer join to the query
     * 
     * @param string $table The table to join data from
     * @return \p810\MySQL\Builder\Grammar\JoinExpression
     */
    public function leftOuterJoin(string $table): JoinExpression
    {
        return $this->join('left outer', $table);
    }

    /**
     * Appends a right outer join to the query
     * 
     * @param string $table The table to join data from
     * @return \p810\MySQL\Builder\Grammar\JoinExpression
     */
    public function rightOuterJoin(string $table): JoinExpression
    {
        return $this->join('right outer', $table);
    }

    /**
     * Appends an "on" clause to the current join with "or" as the logical operator
     * 
     * @param string $left The left hand column
     * @param string $right The right hand column
     * @return \p810\MySQL\Builder\BuilderInterface
     * @throws \BadMethodCallException if this method was called before a `JoinExpression` was instantiated
     */
    public function orOn(string $left, string $right): BuilderInterface
    {
        if (! $this->current) {
            throw new BadMethodCallException(
                '\p810\MySQL\Builder\Grammar\Join::orOn() cannot be called before an expression has been set' .
                'by calling a setter method, e.g. \p810\MySQL\Builder\Grammar\Join::innerJoin()'
            );
        }
        
        return $this->current->on($left, $right, 'or');
    }

    /**
     * Compiles the join clause
     * 
     * @return null|string
     */
    protected function compileJoin(): ?string
    {
        $joins = $this->getParameter('joins');

        if (! $joins) {
            return null;
        }

        return spaces($joins);
    }
}
