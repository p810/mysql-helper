<?php

namespace p810\MySQL\Builder;

class Select extends Builder {
    public function build(): string {
        return sprintf('SELECT %s FROM %s', $this->getColumns(), $this->getTable());
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

    public function setTable(string $table): self {
        $this->fragments['table'] = $table;

        return $this;
    }
}