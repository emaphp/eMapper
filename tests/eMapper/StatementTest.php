<?php
namespace eMapper;

use eMapper\Engine\Generic\Statement\StatementFormatter;

abstract class StatementTest extends \PHPUnit_Framework_TestCase {
	/**
	 * 
	 * @var mixed
	 */
	protected $conn;
	
	/**
	 * 
	 * @var StatementFormatter
	 */
	protected $statement;
	
	abstract function getConnection();
	abstract function getStatement($conn);
	
	public function setUp() {
		$this->conn = $this->getConnection();
		$this->statement = $this->getStatement($this->conn); 
	}
}
?>