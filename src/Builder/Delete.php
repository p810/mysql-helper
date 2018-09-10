<?php

namespace p810\MySQL\Builder;

class Delete extends Builder {
    use \p810\MySQL\Query\Where;
    use \p810\MySQL\Query\From;

    public function build(): string {
        $query = 'DELETE FROM ' . $this->getTable();

        $where = $this->getWhere();
        if ($where !== null) {
            $query .= ' ' . $where;
        }

        return $query;
    }
}