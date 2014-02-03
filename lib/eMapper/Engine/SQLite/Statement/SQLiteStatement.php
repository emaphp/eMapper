<?php
namespace eMapper\Engine\SQLite\Statement;

use eMapper\Engine\Generic\Statement\GenericStatement;
use eMapper\Type\TypeManager;

class SQLiteStatement extends GenericStatement {
	/**
	 * SQLite database
	 * @var \SQLite3
	 */
	public $db;
	
	public function __construct(\SQLite3 $db, TypeManager $typeManager, $parameterMap = null) {
		parent::__construct($typeManager, $parameterMap);
		$this->db = $db;
	}
	
	public function escapeString($string) {
		return $this->db->escapeString($string);
	}
}
?>