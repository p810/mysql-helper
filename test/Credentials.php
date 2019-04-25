<?php

namespace p810\MySQL\Test;

use RuntimeException;
use OutOfBoundsException;
use InvalidArgumentException;

use function stripos;
use function json_decode;
use function file_exists;
use function array_key_exists;
use function file_get_contents;
use function json_last_error_msg;

trait Credentials
{
    /**
     * @var string
     */
    public $host;

    /**
     * @var string
     */
    public $user;

    /**
     * @var string
     */
    public $password;

    /**
     * @var string
     */
    public $database;

    /**
     * @var int
     */
    public $port;

    /**
     * @var string
     */
    public $table;

    /**
     * @return void
     * @throws \OutOfBoundsException
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     * @throws \RuntimeException
     */
    function __construct() {
        $this->loadDatabaseCredentials();
        parent::__construct();
    }

    /**
     * @throws \OutOfBoundsException if $_ENV['database_credentials'] was not set by PHPUnit
     * @throws \InvalidArgumentException if the config file could not be found
     * @throws \RuntimeException if the config file could not be read, or contained no data
     * @throws \RuntimeException if the config file could not be decoded
     */
    public function loadDatabaseCredentials(): bool {
        if (! array_key_exists('database_credentials', $_ENV)) {
            throw new OutOfBoundsException('The database_credentials filepath is not set in $_ENV');
        }

        $filename = $_ENV['database_credentials'];

        if (! stripos($filename, DIRECTORY_SEPARATOR)) {
            $filepath = __DIR__ . '/../' . $filename;
        }  else {
            $filepath = $filename;
        }

        if (! file_exists($filepath)) {
            throw new InvalidArgumentException("Failed to locate the database credentials file at: $filepath");
        }

        $contents = file_get_contents($filepath);

        if (! $contents) {
            throw new RuntimeException("Either file_get_contents() failed, or $filename is empty");
        }

        $contents = json_decode($contents);

        if (! $contents) {
            throw new RuntimeException('json_decode() failed: ' . json_last_error_msg());
        }
        
        $this->user     = $contents->user     ?? 'root';
        $this->host     = $contents->host     ?? '127.0.0.1';
        $this->port     = $contents->port     ?? 3306;
        $this->table    = $contents->table    ?? 'p810_mysql_helper_test';
        $this->password = $contents->password ?? 'root';
        $this->database = $contents->database;

        return true;
    }
}