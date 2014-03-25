<?php
namespace eMapper\Engine\MySQL\Exception;

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