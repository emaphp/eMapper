<?php
namespace eMapper\Engine\SQLite\Exception;

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