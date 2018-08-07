<?php

namespace p810\MySQL;

class Connection {
    /**
     * Database object returned by PDO.
     * @var PDO
     */
    protected $database;

    function __construct(
        string $username, string $password, string $database,
        string $host = '127.0.0.1', ?array $options = null
    ) {
        $arguments = [sprintf('mysql:host=%s;dbname=%s', $host, $database), $username, $password];

        if ($options) {
            $arguments[] = $options;
        }
        
        try {
            $this->database = new \PDO(...$arguments);
        } catch (\PDOException $e) {
            throw new Exception\ConnectionException($e->getMessage(), $e->getCode(), $e);
        }
    }

    public function getResource(): \PDO {
        return $this->database;
    }
}