<?php
namespace eMapper;

abstract class ConnectionTest extends \PHPUnit_Framework_TestCase {
	/**
	 * Database connection
	 * @var mixed
	 */
	protected $conn;
	
	abstract function getConnection();
	
	public function setUp() {
		$this->conn = $this->getConnection();
	}
}
?>