<?php

namespace p810\MySQL\Test\Mapper;

use p810\MySQL\Connection;
use PHPUnit\Framework\TestCase;
use p810\MySQL\Test\Credentials;
use p810\MySQL\ConnectionInterface;
use p810\MySQL\Mapper\DefaultAdapter;

class DefaultAdapterTest extends TestCase
{
    use Credentials;

    /**
     * @var \p810\MySQL\Mapper\AdapterInterface
     */
    protected $adapter;

    public function setUp()
    {
        $this->adapter = new DefaultAdapter($this->getConnection());
    }

    protected function getConnection(): ConnectionInterface
    {
        return new Connection($this->user, $this->password, $this->database, $this->host);
    }

    public function test_data_is_returned_from_adapter()
    {
        $data = $this->adapter->get('test_table')->where('test_id', 1)->execute();

        $this->assertNotNull($data);
    }
}