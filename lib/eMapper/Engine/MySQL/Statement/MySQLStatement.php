<?php
namespace eMapper\Engine\MySQL\Statement;

use eMapper\Engine\Generic\Statement\StatementFormatter;

/**
 * The MySQLStatement class builds queries which are sent to the MySQL database server.
 * @author emaphp
 */
class MySQLStatement extends StatementFormatter {	
	public function escapeString($string) {
		return $this->driver->getConnection()->real_escape_string($string);
	}
}