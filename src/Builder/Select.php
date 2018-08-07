<?php

namespace p810\MySQL\Builder;

class Select extends Builder {
    use \p810\MySQL\Query\Where;

    public function build(): string {
        $query = sprintf('SELECT %s FROM %s', $this->getColumns(), $this->getTable());

        $where = $this->getWhere();
        if ($where !== null) {
            $query .= ' ' . $where;
        }

        return $query;
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
}