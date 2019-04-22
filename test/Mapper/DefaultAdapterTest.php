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

        $this->assertNotEmpty($data);
    }

    public function test_row_is_created_by_adapter()
    {
        $query = $this->adapter->create('test_table', [
            'message' => 'Hello world!'
        ]);

        $this->assertEquals(1, $query->execute());
    }

    public function test_row_is_updated_by_adapter()
    {
        $query = $this->adapter->save('test_table');
        $query->set('message', sprintf('Hello world! I am being updated at: %s', microtime(true)));
        $query->where('test_id', 2);

        $this->assertEquals(1, $query->execute());
    }

    public function test_row_is_deleted_by_adapter()
    {
        $query = $this->adapter->delete('test_table')->where('message', 'Hello world!');

        $this->assertEquals(1, $query->execute());
    }
}