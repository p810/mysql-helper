<?php

namespace p810\MySQL\Builder;

class Select extends Builder {
    use \p810\MySQL\Query\Where;
    use \p810\MySQL\Query\From;

    public function build(): string {
        $query = sprintf('SELECT %s FROM %s', $this->getColumns(), $this->getTable());

        $where = $this->getWhere();
        if ($where !== null) {
            $query .= ' ' . $where;
        }

        return $query;
    }
}