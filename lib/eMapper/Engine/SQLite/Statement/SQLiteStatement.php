<?php
namespace eMapper\Engine\SQLite\Statement;

use eMapper\Engine\Generic\Statement\StatementFormatter;

/**
 * The SQLiteStatement class builds query string that are executed against a SQLite database.
 * @author emaphp
 */
class SQLiteStatement extends StatementFormatter {
	public function escapeString($string) {
		return $this->driver->getConnection()->escapeString($string);
	}
}