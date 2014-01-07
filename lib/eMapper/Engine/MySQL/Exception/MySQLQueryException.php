<?php
namespace eMapper\Engine\MySQL\Exception;

use eMapper\Engine\MySQL\Exception\MySQLMapperException;

class MySQLQueryException extends MySQLMapperException {
	public function __construct($message, $query) {
		parent::__construct($message);
		$this->query = $query;
	}
	
	public function getQuery() {
		return $this->query;
	}
}