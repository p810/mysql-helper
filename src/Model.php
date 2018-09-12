<?php

namespace p810\MySQL;

use p810\MySQL\Model\Row;
use p810\MySQL\Builder\Select;

abstract class Model {
    /**
     * The table represented by this model.
     * @var string
     */
    protected $table;

    /**
     * An instance of PDO obtained from the
     * Connection instance.
     * @var \PDO
     */
    protected $database;

    /**
     * The column set as the table's primary key.
     * @var string
     */
    protected $primaryKey;

    function __construct(Connection $connection) {
        $this->database = $connection->getResource();

        if (! Query::isConnected()) {
            Query::setConnection($connection);
        }

        if (! $this->primaryKey) {
            $column = $this->queryForPrimaryKey();

            if ($column) {
                $this->primaryKey = $column;
            }
        }
    }

    protected function queryForPrimaryKey(): ?string {
        $query = $this->database->query("SHOW KEYS FROM {$this->table} WHERE Key_name = 'primary'");

        if ($query->execute() === false) {
            return null;
        }

        $results = $query->fetchAll(\PDO::FETCH_ASSOC);

        if (empty($results)) {
            return null;
        }

        $data = $results[0];

        return $data['Column_name'];
    }

    final public function getTable(): string {
        return $this->table;
    }

    final public function getPrimaryKey(): ?string {
        return $this->primaryKey;
    }

    public function where(...$clauses): ?array {
        $data = Query::select()
            ->from( $this->getTable() )
            ->where(...$clauses)
            ->execute();

        if (! $data || empty($data)) {
            return null;
        }

        $rows = [];
        foreach ($data as $row) {
            $rows[] = new Row($row, $this);
        }

        return $rows;
    }

    public function create(array $data): ?array {
        $primaryKey = $this->getPrimaryKey();

        $id = Query::insert( $this->getTable() )
            ->columns( array_keys($data) )
            ->values( array_values($data) )
            ->execute();

        if (! $id || ! $primaryKey) {
            return null;
        }

        return $this->where($primaryKey, $id);
    }

    public function delete(...$clauses): bool {
        $deleted = Query::delete( $this->getTable() )
            ->where(...$clauses)
            ->execute();

        return (bool) $deleted;
    }

    public function count(): ?int {
        $query = Query::select('COUNT(*)')
            ->from( $this->getTable() )
            ->execute();

        if (! $query) {
            return null;
        }

        return $query[0]['COUNT(*)'];
    }
}