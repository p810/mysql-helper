<?php

namespace p810\MySQL;

class Row {
    /**
     * @param array $data Data returned from PDO (column => value).
     */
    function __construct(array $data) {
        foreach ($data as $column => $value) {
            $this->{$column} = $value;
        }
    }
}