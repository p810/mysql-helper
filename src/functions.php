<?php

namespace p810\MySQL;

use p810\MySQL\Query;
use p810\MySQL\Builder\Builder;

function select($columns = '*'): Builder
{
    return Query::select($columns);
}

function insert(string $table): Builder
{
    return Query::insert($table);
}

function update(string $table): Builder
{
    return Query::update($table);
}

function delete(): Builder
{
    return Query::delete();
}