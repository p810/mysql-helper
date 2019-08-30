<?php

namespace p810\MySQL\Test;

use PHPUnit\Framework\TestCase;

use function p810\MySQL\spaces;
use function p810\MySQL\commas;
use function p810\MySQL\makePdoDsn;
use function p810\MySQL\parentheses;

class HelperFunctionsTest extends TestCase
{
    public function test_spaces()
    {
        $this->assertEquals('hello world', spaces(['hello', 'world']));
    }

    public function test_commas()
    {
        $this->assertEquals('hello, world', commas(['hello', 'world']));
    }

    public function test_parentheses_with_array()
    {
        $this->assertEquals('(hello, world)', parentheses(['hello', 'world']));
    }

    public function test_parentheses_with_string()
    {
        $this->assertEquals('(hello world)', parentheses('hello world'));
    }

    public function test_dsn_is_made()
    {
        $expected = 'mysql:host=localhost;dbname=test_db;foo=bar;bam=quux';
        
        $actual = makePdoDsn('localhost', 'test_db', [
            'foo' => 'bar',
            'bam' => 'quux'
        ]);

        $this->assertEquals($expected, $actual);
    }
}
