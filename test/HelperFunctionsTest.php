<?php

namespace p810\MySQL\Test;

use PHPUnit\Framework\TestCase;

use function p810\MySQL\coalesce;
use function p810\MySQL\spaces;
use function p810\MySQL\commas;
use function p810\MySQL\greatest;
use function p810\MySQL\keywordToString;
use function p810\MySQL\least;
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

    public function test_keyword_has_string_representation()
    {
        $this->assertEquals('null', keywordToString(null));
        $this->assertEquals('true', keywordToString(true));
        $this->assertEquals('false', keywordToString(false));
        $this->assertEquals('unknown', keywordToString(null, true));
    }

    public function test_greatest_returns_formatted_function_call()
    {
        $this->assertEquals('greatest(1, 2, 3)', greatest([1, 2, 3]));
    }

    public function test_least_returns_formatted_function_call()
    {
        $this->assertEquals('least(1, 2, 3)', least([1, 2, 3]));
    }

    public function test_coalesce_returns_formatted_function_call()
    {
        $this->assertEquals('coalesce(null, 1)', coalesce([null, 1]));
    }
}
