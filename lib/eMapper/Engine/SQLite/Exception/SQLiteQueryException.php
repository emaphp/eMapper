<?php
namespace eMapper\Engine\SQLite\Exception;

/**
 * The SQLiteQueryException class identifies SQLite query errors.
 * @author emaphp
 */
class SQLiteQueryException extends SQLiteException {
	/**
	 * Query string
	 * @var string
	 */
	protected $query;
	
	public function __construct($message, $query) {
		parent::__construct($message);
		$this->query = $query;
	}
	
	public function getQuery() {
		return $this->query;
	}
}
?>