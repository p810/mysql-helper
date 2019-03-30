<?php

namespace p810\MySQL\Builder;

use PDOStatement;

class Update extends Builder
{
    use \p810\MySQL\Query\From;
    use \p810\MySQL\Query\Where;
    use \p810\MySQL\Query\Values;

    public function build(): string
    {
        $query = sprintf('UPDATE %s SET %s', $this->getTable(), $this->getValues());

        $where = $this->getWhere();
        if ($where !== null) {
            $query .= ' ' . $where;
        }

        return $query;
    }

    protected function handleResults(PDOStatement $statement)
    {
        return $statement->rowCount();
    }
}