<?php
namespace eMapper\Type\Handler;

use eMapper\Type\TypeHandler;

class DatetimeTypeHandler extends TypeHandler {
	/**
	 * Custom time zone
	 * @var \DateTimeZone
	 */
	public $tz;
	
	public function __construct(\DateTimeZone $tz = null) {
		$this->tz = $tz;
	}
	
	public function getValue($value) {
		try {
			return is_null($this->tz) ? new \DateTime($value) : new \DateTime($value, $this->tz);
		}
		catch (\Exception $e) {
			throw new \UnexpectedValueException($e->getMessage());
		}
	}
	
	public function castParameter($parameter) {
		if ($parameter instanceof \DateTime)
			return $parameter;
		elseif (is_string($parameter))
			return is_null($this->tz) ? new \DateTime($parameter) : new \DateTime($parameter, $this->tz);
		elseif (is_integer($parameter))
			return is_null($this->tz) ? new \DateTime('@' . $parameter) : new \DateTime('@' . $parameter, $this->tz);
		
		return null;
	}
	
	public function setParameter($parameter) {
		if (!($parameter instanceof \DateTime))
			throw new \InvalidArgumentException("Type handler expected an instance of DateTime");
		
		return $parameter->format('Y-m-d H:i:s');
	}
}
?>