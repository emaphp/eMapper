<?php
namespace eMapper\Engine\SQLite\Statement;

use eMapper\Engine\Generic\Statement\GenericStatement;
use eMapper\Type\TypeManager;

/**
 * The SQLiteStatement class builds query string that are executed against a SQLite database.
 * @author emaphp
 */
class SQLiteStatement extends GenericStatement {
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