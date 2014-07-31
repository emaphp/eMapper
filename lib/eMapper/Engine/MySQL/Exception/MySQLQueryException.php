<?php
namespace eMapper\Engine\MySQL\Exception;

/**
 * The MySQLQueryException class identifies MySQL database query errors.
 * @author emaphp
 */
class MySQLQueryException extends MySQLException {
	/**
	 * Failed query string
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