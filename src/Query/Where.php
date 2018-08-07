<?php

namespace p810\MySQL\Query;

use p810\MySQL\Exception\QueryBuildException;

trait Where {
    public function where(...$arguments): self {
        if (count($arguments) === 1 && is_array($arguments[0])) {
            foreach ($arguments[0] as $column => $condition) {
                $data = [];

                if (is_array($condition)) {
                    switch (count($condition)) {
                        case 1:
                            array_push($condition, '=', 'AND');
                            break;
                        case 2:
                            array_push($condition, 'AND');
                            break;
                    }

                    $data = $condition;
                } else {
                    array_push($data, $condition, '=', 'AND');
                }

                array_unshift($data, $column);

                $this->setWhere(...$data);
            }

            return $this;
        }

        return $this->setWhere(...$arguments);
    }

    public function and(string $column, $value, string $operator = '='): self {
        return $this->setWhere($column, $value, $operator);
    }

    public function or(string $column, $value, string $operator = '='): self {
        return $this->setWhere($column, $value, $operator, 'OR');
    }

    protected function setWhere(string $column, $value, string $comparison = '=', string $operator = 'AND'): self {
        $this->fragments['where'][$column] = [
            'comparison' => $comparison,
            'value'      => $value,
            'operator'   => $operator
        ];

        return $this;
    }

    public function getWhere(): ?string {
        if (! array_key_exists('where', $this->fragments) || empty($this->fragments['where'])) {
            return null;
        }

        $where = 'WHERE ';
        
        foreach ($this->fragments['where'] as $column => $data) {
            [$comparison, $value, $operator] = array_values($data);

            $where .= "$column $comparison '$value'";

            if (count($this->fragments['where']) > 1) {
                end($this->fragments['where']);

                if (key($this->fragments['where']) !== $column) {
                    $operator = $this->getNextOperator($column);

                    if (! $operator) {
                        throw new QueryBuildException('Could not join conditions due to a missing operator');
                    }

                    $where .= " $operator ";
                }
            }
        }

        return $where;
    }

    /**
     * To accurately concatenate conditions in a WHERE clause
     * the operators between conditions should be found by
     * looking ahead from the current position in an iteration.
     * 
     * For example, for this clause:
     *      `WHERE foo = 'bar' OR bar = 'foo'`
     * the first condition would look ahead to the second one to
     * get the OR operator.
     */
    private function getNextOperator(string $column): ?string {
        reset($this->fragments['where']);

        while (key($this->fragments['where']) !== $column) {
            next($this->fragments['where']);
        }
        
        next($this->fragments['where']);

        $key = key($this->fragments['where']);

        return $this->fragments['where'][$key]['operator'];
    }
}