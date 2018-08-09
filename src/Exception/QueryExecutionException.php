<?php

namespace p810\MySQL\Exception;

use \Throwable;

class QueryExecutionException extends \Exception {
    function __construct(string $message = '', int $code = 0, ?Throwable $previous = null) {
        // If a PDOException was thrown, lets use its message since it
        // will be helpful for debugging why the query couldn't execute
        if ($previous && ($previous instanceof \PDOException)) {
            $message = $previous->getMessage();
        }

        parent::__construct($message, $code, $previous);
    }
}