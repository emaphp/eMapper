<?php
namespace eMapper\Engine\MySQL\Statement;

use eMapper\Type\TypeManager;
use eMapper\Engine\Generic\Statement\GenericStatement;

/**
 * The MySQLStatement class builds queries which are sent to the MySQL database server.
 * @author emaphp
 */
class MySQLStatement extends GenericStatement {
	/**
	 * MySQL connection
	 * @var mysqli
	 */
	protected $conn;

	public function __construct($conn, TypeManager $typeManager, $parameterMap = null) {
		parent::__construct($typeManager, $parameterMap);
		$this->conn = $conn;
	}
	
	public function escapeString($string) {
		return $this->conn->real_escape_string($string);
	}
}