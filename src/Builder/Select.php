<?php

namespace p810\MySQL\Builder;

use p810\MySQL\Row;
use p810\MySQL\ResultSet;

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

    protected function handleResults(\PDOStatement $statement): array {
        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }
}