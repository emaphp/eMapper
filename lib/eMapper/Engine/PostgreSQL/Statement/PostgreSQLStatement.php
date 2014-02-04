<?php
namespace eMapper\Engine\PostgreSQL\Statement;

use eMapper\Engine\Generic\Statement\GenericStatement;
use eMapper\Type\TypeManager;

class PostgreSQLStatement extends GenericStatement {
	/**
	 * PostgreSQL connection
	 * @var resource
	 */
	public $connection;
	
	public function __construct($connection, TypeManager $typeManager, $parameterMap = null) {
		parent::__construct($typeManager, $parameterMap);
		$this->connection = $connection;
	}
	
	public function escapeString($string) {
		return pg_escape_string($this->connection, $string);
	}
}
?>