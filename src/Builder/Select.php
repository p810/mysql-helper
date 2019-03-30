<?php

namespace p810\MySQL\Builder;

use PDO;
use PDOStatement;
use p810\MySQL\Row;
use p810\MySQL\ResultSet;

use function sprintf;

class Select extends Builder
{
    use \p810\MySQL\Query\Where;
    use \p810\MySQL\Query\From;

    public function build(): string
    {
        $query = sprintf('SELECT %s FROM %s', $this->getColumns(), $this->getTable());

        $alias = $this->getAlias();
        if ($alias) {
            $query .= " AS $alias";
        }

        $where = $this->getWhere();
        if ($where !== null) {
            $query .= " $where";
        }

        return $query;
    }

    protected function handleResults(PDOStatement $statement): array
    {
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }
}