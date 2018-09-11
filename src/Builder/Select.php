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

    protected function handleResults(\PDOStatement $result): ?ResultSet {
        $results = array_map(function ($row) {
            return new Row($row);
        }, $statement->fetchAll(\PDO::FETCH_ASSOC));

        if (! empty($results)) {
            $set = new ResultSet;
            foreach ($results as $result) {
                $set->attach($result);
            }

            return $set;
        }

        return null;
    }
}