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
	public function escapeString($string) {
		return pg_escape_string($this->driver->getConnection(), $string);
	}
}
?>