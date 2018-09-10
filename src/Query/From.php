<?php

namespace p810\MySQL\Query;

trait From {
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

    /**
     * An alias of From::setTable() for looks.
     */
    public function from(string $table): self {
        return $this->setTable($table);
    }

    public function columns($columns): self {
        return $this->setColumns($columns);
    }
}