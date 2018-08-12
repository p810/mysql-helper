<?php

namespace p810\MySQL\Query;

use p810\MySQL\Exception\QueryBuildException;

trait Where {
    /**
     * Prepares a WHERE clause to be appended to the query string.
     * 
     * This is a flexible method that may be called a number of ways.
     * Some examples:
     * 
     * - where('column', 'value')
     * - where('column', '>=', 'value')
     * - where( ['column' => 'value', 'column2' => ['!=', 'value2', 'OR']] )
     * - where('column', '=', 'value', 'AND')
     */
    public function where(...$arguments): self {
        // Assume this is a list of arguments if only one is supplied
        if (count($arguments) === 1) {
            $conditions = array_shift($arguments);

            foreach ($conditions as $column => $data) {
                if (is_array($data)) {
                    $this->where($column, ...$data);
                } else {
                    $this->where($column, $data);
                }
            }

            return $this;
        }

        $column = array_shift($arguments);

        if (is_array($arguments[0])) {
            return $this->where($column, ...$arguments[0]);
        }

        switch (count($arguments)) {
            case 1:
                $comparison = '=';
                $value      = $arguments[0];
                $operator   = 'AND';
                break;
            case 2:
                $comparison = $arguments[0];
                $value      = $arguments[1];
                $operator   = 'AND';
                break;
            default:
                [$comparison, $value, $operator] = $arguments;
                break;
        }

        return $this->setWhere($column, $value, $comparison, $operator);
    }

    public function and(...$arguments): self {
        return $this->where(...$arguments);
    }

    public function or(...$arguments): self {
        if (count($arguments) === 4) {
            $arguments[4] = 'OR';
        } else {
            array_push($arguments, 'OR');
        }

        return $this->where(...$arguments);
    }

    protected function setWhere(string $column, $value, string $comparison = '=', string $operator = 'AND'): self {
        $this->bind($value);
        
        $this->fragments['where'][$column] = [
            'comparison' => $comparison,
            'value'      => '?',
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

            $where .= "$column $comparison $value";

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