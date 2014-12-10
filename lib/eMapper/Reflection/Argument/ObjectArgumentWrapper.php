<?php
namespace eMapper\Reflection\Argument;

use eMapper\Reflection\Profiler;

/**
 * The ObjectArgumentWrapper class provides an interface for accessing properties.
 * @author emaphp
 */
class ObjectArgumentWrapper extends ArgumentWrapper {
	/**
	 * Class profile
	 * @var \eMapper\Reflection\ClassProfile
	 */
	protected $profile;

	public function __construct($value) {
		$this->value = $value;
		$this->profile = Profiler::getClassProfile(get_class($value));
	}
	
	public function getClassProfile() {
		return $this->profile;
	}

	public function offsetExists($offset) {
		return $this->profile->hasProperty($offset);
	}
	
	public function offsetGet($offset) {
		$property = $this->profile->getProperty($offset)->getReflectionProperty();
		
		if (!$property->isPublic())
			$property->setAccessible(true);
		
		return $property->getValue($this->value);
	}
	
	public function offsetSet($offset, $value) {
		$property = $this->profile->getProperty($offset)->getReflectionProperty();
		
		if (!$property->isPublic())
			$property->setAccessible(true);
			
		return $property->setValue($this->value, $value);
	}
	
	public function offsetUnset($offset) {
		$property = $this->profile->getProperty($offset)->getReflectionProperty();
		
		if (!$property->isPublic())
			$property->setAccessible(true);
			
		return $property->setValue($this->value, null);
	}
}
