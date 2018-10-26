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

    public function from(string $table): self {
        return $this->setTable($table);
    }

    public function columns($columns): self {
        return $this->setColumns($columns);
    }

    public function as(string $alias): self {
        return $this->setAlias($alias);
    }

    public function setAlias(string $alias) {
        $this->fragments['as'] = $alias;

        return $this;
    }

    public function getAlias(): ?string {
        return isset($this->fragments['as']) ? $this->fragments['as'] : null;
    }
}