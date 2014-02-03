<?php
namespace eMapper\Engine\SQLite\Exception;

class SQLiteQueryException extends SQLiteMapperException {
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