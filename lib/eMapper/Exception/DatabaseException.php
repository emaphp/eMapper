<?php
namespace eMapper\Exception;

abstract class DatabaseException extends \Exception {
	public function __construct($message, \Exception $previous = null) {
		parent::__construct($message, 0, $previous);
	}
}
?>