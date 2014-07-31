<?php
namespace eMapper\Exception;

/**
 * The DatabaseException class identifies an exception related to a connection error
 * or bad query execution.
 * @author emaphp
 */
abstract class DatabaseException extends \Exception {
	public function __construct($message, \Exception $previous = null) {
		parent::__construct($message, 0, $previous);
	}
}
?>