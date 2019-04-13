<?php

namespace p810\MySQL\Builder;

class Select extends Builder
{
    use Grammar\Where;

    /** @inheritdoc */
    protected $order = ['select', 'from', 'where', 'limit'];

    public function from(string $table): self
    {
        return $this->append(Token::FROM, $table);
    }

    public function select($columns): self
    {
        if (is_array($columns)) {
            $columns = implode(', ', $columns);
        }

        return $this->append(Token::SELECT, $columns);
    }

    public function limit(int $limit): self
    {
        return $this->append(Token::LIMIT, $limit);
    }
}