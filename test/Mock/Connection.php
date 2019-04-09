<?php

namespace p810\MySQL\Test\Mock;

use PDO;
use p810\MySQL\Query;
use p810\MySQL\ConnectionInterface;
use p810\MySQL\Builder\{Select, Insert, Update, Delete};

trait Connection
{
    public function getMockConnection(): ConnectionInterface
    {
        return new class implements ConnectionInterface {
            public function getPdo(): PDO {
                return new PDO;
            }

            public function select(): Query {
                return new Query($this, new Select);
            }

            public function insert(): Query {
                return new Query($this, new Insert);
            }

            public function update(): Query {
                return new Query($this, new Update);
            }

            public function delete(): Query {
                return new Query($this, new Delete);
            }
        };
    }
}