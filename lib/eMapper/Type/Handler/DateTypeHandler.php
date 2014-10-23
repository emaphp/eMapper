<?php
namespace eMapper\Type\Handler;

use eMapper\Type\Handler\DatetimeTypeHandler;

class DateTypeHandler extends DatetimeTypeHandler {
	public function setParameter($parameter) {
		if (!($parameter instanceof \DateTime))
			throw new \InvalidArgumentException("Type handler expected an instance of DateTime");
		
		return $parameter->format('Y-m-d');
	}
}
?>