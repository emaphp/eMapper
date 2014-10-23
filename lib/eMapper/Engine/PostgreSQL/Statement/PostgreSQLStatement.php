<?php
namespace eMapper\Engine\PostgreSQL\Statement;

use eMapper\Type\TypeManager;
use eMapper\Engine\Generic\Statement\StatementFormatter;

/**
 * The PostgreSQLStatement class generates queries that are sent to the PostgreSQL
 * database server.
 * @author emaphp
 */
class PostgreSQLStatement extends StatementFormatter {
	/**
	 * PostgreSQL connection
	 * @var resource
	 */
	protected $connection;
	
	public function __construct($connection, TypeManager $typeManager, $parameterMap = null) {
		parent::__construct($typeManager, $parameterMap);
		$this->connection = $connection;
	}
	
	public function escapeString($string) {
		return pg_escape_string($this->connection, $string);
	}
}
?>