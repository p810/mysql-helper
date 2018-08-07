<?php

namespace p810\MySQL\Builder;

class Select extends Builder {
    public function build(): string {
        return sprintf(
            'SELECT %s FROM %s %s',
            $this->getColumns(), $this->getTable(), $this->getWhere()
        );
    }

    public function getColumns(): string {
        if (is_array($this->fragments['columns'])) {
            return implode(', ', $this->fragments['columns']);
        }

        return $this->fragments['columns'];
    }

    public function setColumns($columns): self {
        $this->fragments['columns'] = $columns;

        return $this;
    }

    public function getTable(): string {
        return $this->fragments['table'];
    }

    public function from(string $table): self {
        $this->fragments['table'] = $table;

        return $this;
    }

    public function getWhere(): string {
        if (!isset($this->fragments['where'])) {
            return '';
        }

        $clauseString = 'WHERE ';
        foreach ($this->fragments['where'] as $column => $clause) {
            if (is_array($clause)) {
                [$operator, $value] = $clause;
            } else {
                $operator = '=';
                
                $value = $clause;
            }

            $clauseString .= "$column $operator '$value'";

            if (count($this->fragments['where']) > 1) {
                end($this->fragments['where']);

                if (key($this->fragments['where']) !== $column) {
                    $clauseString .= ' AND ';
                }
            }
        }
        
        return $clauseString;
    }

    public function where(array $clauses): self {
        $this->fragments['where'] = $clauses;

        return $this;
    }
}