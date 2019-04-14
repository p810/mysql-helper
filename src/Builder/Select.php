<?php

namespace p810\MySQL\Builder;

use function key;
use function implode;
use function is_array;
use function is_string;
use function array_walk;

class Select extends Builder
{
    use Grammar\Where;

    /**
     * @inheritdoc
     */
    protected $order = [
        Token::SELECT,
        Token::FROM,
        Token::WHERE,
        Token::LIMIT
    ];

    public function from(string $table): self
    {
        return $this->append(Token::FROM, $table);
    }

    /**
     * @param string[]|string|array<string,string> $columns
     */
    public function select($columns): self
    {
        if (is_array($columns)) {
            $isAssoc = is_string(key($columns));

            if ($isAssoc) {
                $columns = $this->prefixColumnsWithTable($columns);
            }

            $columns = implode(', ', $columns);
        }

        return $this->append(Token::SELECT, $columns);
    }

    public function limit(int $limit): self
    {
        return $this->append(Token::LIMIT, $limit);
    }

    /**
     * @param array<string,string> $columns
     * @return string[]
     */
    protected function prefixColumnsWithTable(array $columns): array
    {
        array_walk($columns, function (&$column, $table) {
            $column = "$table.$column";
        });

        return $columns;
    }
}