<?php
namespace eMapper\Engine\SQLite\Statement;

use eMapper\Type\TypeManager;
use eMapper\Engine\Generic\Statement\StatementFormatter;

/**
 * The SQLiteStatement class builds query string that are executed against a SQLite database.
 * @author emaphp
 */
class SQLiteStatement extends StatementFormatter {
	/**
	 * SQLite database
	 * @var \SQLite3
	 */
	protected $db;
	
	public function __construct(\SQLite3 $db, TypeManager $typeManager, $parameterMap = null) {
		parent::__construct($typeManager, $parameterMap);
		$this->db = $db;
	}
	
	public function escapeString($string) {
		return $this->db->escapeString($string);
	}
}
?>