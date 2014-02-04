<?php
namespace eMapper\Engine\PostgreSQL\Exception;

class PostgreSQLQueryException extends PostgreSQLMapperException {
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