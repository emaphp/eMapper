<?php
namespace eMapper\Exception;

abstract class MapperException extends \Exception {
	public function __construct($message) {
		parent::__construct($message);
	}
}
?>