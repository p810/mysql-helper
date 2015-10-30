<?php

namespace p810\MySQL\Relationships;

use \p810\MySQL\Helpers\Table as TableHelper;

abstract class Query
{
    /**
     * Maps a one-to-one relationship between two tables.
     *
     * This method will select all columns from $table where $key is equal to the ID contained in
     * Relationship::$id. Example:
     *
     * > select * from `profiles` where `user_id` = 1;
     *
     * Unlike Query::hasMany(), this method will limit the resultset to one row.
     *
     * @param $table string The name of the foreign table.
     * @param $key string The primary key of the local table.
     * @return bool|array
     */
    protected function hasOne($table, $key)
    {
        $query = $this->resource->select($this->columns, $table);

        $query->where($key, $this->id);
        $query->limit(1);

        $result = $query->execute();

        if (count($result) > 0) {
            $result = array_shift($result);
        } else {
            $result = false;
        }

        return $result;
    }

    
    /**
     * Maps a one-to-many relationship between two tables.
     *
     * This method will select all columns from $table where $key is equal to the ID contained in
     * Relationship::$id. Example:
     *
     * > select * from `posts` where `user_id` = 1;
     *
     * @param $table string The name of the foreign table.
     * @param $key string The primary key of the local table.
     * @return bool|array
     */
    protected function hasMany($table, $key)
    {
        $query = $this->resource->select($this->columns, $table);

        $query->where($key, $this->id);

        $result = $query->execute();

        if (count($result) === 0) {
            $result = false;
        }

        return $result;
    }


    /**
     * Maps an inverse one-to-one relationship between two tables.
     *
     * This method functions the same as Query::hasOne() except that the primary $key will
     * correspond to the foreign table and not the local table. Example:
     *
     * > select * from `users` where `user_id` = 1;
     *
     * @param $table string The name of the foreign table.
     * @param $key string The primary key of the foreign table.
     * @return bool|array
     */
    protected function belongsToOne($table, $key)
    {
        $query = $this->resource->select($this->columns, $table);

        $query->where($key, $this->id);
        $query->limit(1);

        $result = $query->execute();

        if (count($result) > 0) {
            $result = array_shift($result);
        } else {
            $result = false;
        }

        return $result;
    }


    /**
     * Maps a many-to-many relationship between two tables, through an intermediary.
     *
     * This method performs two queries. The first will find all rows in $intermediary where the
     * $key is equal to the ID contained in Relationship::$id. Then, the foreign table will be queried
     * for each matching row, and the results will be stored. Example:
     *
     * > select * from `roles_to_permissions` where `role_id` = 1;
     *
     * Then the loop, where :id is the ID of the result:
     *
     * > select * from `permissions` where `permission_id` = :id;
     *
     * @param $table string The name of the foreign table.
     * @param $key string The primary key of the local table.
     * @param $intermediary string The name of the intermediary table.
     * @return bool|array
     */
    protected function belongsToMany($table, $key, $intermediary)
    {
        $query = $this->resource->select($this->columns, $intermediary);

        $query->where($key, $this->id);

        $results = $query->execute();

        if (count($results) > 0) {
            $return = array();

            $primary_key = TableHelper::getPrimaryKey($table);

            foreach ($results as $result) {
                $query = $this->resource->select('*', $table);

                $query->where($primary_key, $result[$primary_key]);

                $result = $query->execute();

                $return[] = array_shift($result);
            }

            $results = $return;
        } else {
            $results = false;
        }

        return $results;
    }
}