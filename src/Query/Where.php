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
        switch (count($arguments)) {
            case 1:
                if (! is_array($arguments[0])) {
                    throw new \UnexpectedValueException;
                }
                
                foreach ($arguments[0] as $column => $data) {
                    if (is_array($data)) {
                        $this->where($column, ...$data);
                    } else {
                        $this->where($column, $data);
                    }
                }

                return $this;
            break;
            
            case 2:
                $arguments = [$arguments[0], '=', $arguments[1], 'AND'];    
            break;
            
            case 3:
                array_push($arguments, 'AND');   
            break;
        }

        return $this->setWhere([
            'column'     => $arguments[0],
            'value'      => $arguments[2],
            'operator'   => $arguments[3],
            'comparison' => $arguments[1]
        ]);
    }

    public function and(...$arguments): self {
        return $this->where(...$arguments);
    }

    public function or(...$arguments): self {
        switch (count($arguments)) {
            case 1:
                if (! is_array($arguments[0])) {
                    /** @todo: improve this exception (?) */
                    throw new \UnexpectedValueException;
                }

                foreach ($arguments[0] as $column => $data) {
                    $this->or($column, ...$data);
                }

                return $this;
            break;
            
            case 2:
                $arguments = [$arguments[0], '=', $arguments[1], 'OR'];
            break;
            
            case 3:
                array_push($arguments, 'OR');
            break;
            
            case 4:
                if ($arguments[3] !== 'OR') {
                    $arguments[3] = 'OR';
                }
            break;
        }

        return $this->where(...$arguments);
    }

    protected function setWhere(array $data): self {
        $this->bind($data['value']);

        $data['value'] = '?';
        
        $this->fragments['where'][ $data['column'] ] = [
            'comparison' => $data['comparison'],
            'value'      => '?',
            'operator'   => $data['operator']
        ];

        return $this;
    }

    public function getWhere(): ?string {
        if (! array_key_exists('where', $this->fragments) || empty($this->fragments['where'])) {
            return null;
        }

        $where = 'WHERE ';
        foreach ($this->fragments['where'] as $column => $data) {
            list (
                'value'      => $value,
                'comparison' => $comparison,
                'operator'   => $operator
            ) = $data;

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