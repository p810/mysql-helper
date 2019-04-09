<?php

namespace p810\MySQL\Exception;

use Exception;
use Throwable;
use PDOException;

class QueryExecutionException extends Exception
{
    function __construct(string $message = '', int $code = 0, ?Throwable $previous = null)
    {
        if ($previous && $previous instanceof PDOException) {
            $message = $previous->getMessage();
        }

        parent::__construct($message, $code, $previous);
    }
}