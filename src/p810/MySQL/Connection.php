<?php namespace p810\MySQL;

use \PDO;
use \PDOException;
use p810\MySQL\Query;
use p810\MySQL\Exceptions\MySQLConnectionException;

class Connection
{
  /**
   * References an instance of PDO.
   *
   * @access protected
   * @var object
   */
  protected $resource;


  /**
   * References an instance of Query.
   *
   * @access public
   * @var object
   */
  public $query;


  /**
   * Creates an instance of PDO for the object to wrap around.
   *
   * @param string $username The username of the MySQL user.
   * @param string $password The password corresponding to $username.
   * @param string $database The database to be accessed.
   * @param string $host (Optional) The host to connect to, defaults to localhost.
   * @return void
   * @throws MySQLConnectionException if PDO could not connect to the database.
   */
  function __construct($username, $password, $database, $host = 'localhost')
  {
    try {
      $this->resource = new PDO('mysql:host=' . $host . ';dbname=' . $database, $username, $password);
    } catch(PDOException $e) {
      throw new MySQLConnectionException($e->getMessage());
    }

    $this->query = new Query($this);
  }


  /**
   * Returns this object's link to the database.
   *
   * @return object
   * @see self::$resource
   */
  public function getResource()
  {
    return $this->resource;
  }
}