<?php
namespace eMapper\Engine\PostgreSQL\Exception;

/**
 * The PostgreSQLQueryException class identifies PostgreSQL database
 * query errors.
 * @author emaphp
 */
class PostgreSQLQueryException extends PostgreSQLException {
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