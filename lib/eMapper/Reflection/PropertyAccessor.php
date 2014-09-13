<?php
namespace eMapper\Reflection;

use eMapper\Reflection\Profile\PropertyProfile;

trait PropertyAccessor {
	/**
	 * Sets a property/key value
	 * @param ClassProfile $profile
	 * @param mixed $instance
	 * @param string $property
	 * @param mixed $value
	 * @throws \RuntimeException
	 */
	protected function setPropertyValue($profile, &$instance, $property, $value) {
		$attr = $profile->getProperty($property);
		
		if ($profile->getReflectionClass()->isInstance($instance)) {
			$attr->getReflectionProperty()->setValue($instance, $value);
			return;
		}
	
		$name = $attr->getName();
	
		if ($instance instanceof \stdClass) {
			$instance->$name = $value;
		}
		elseif (is_array($instance) || $instance instanceof \ArrayObject) {
			$instance[$name] = $value;
		}
		elseif (property_exists($instance, $name)) {
			$rc = new \ReflectionClass(get_class($instance));
			$alias = $rc->getProperty($name);
			$alias->setAccessible(true);
			$alias->setValue($instance, $value);
		}
		else {
			throw new \RuntimeException(sprintf("Property %s was not found on instance of class %s", $name, get_class($instance)));
		}
	}
	
	/**
	 * Gets a property/key value
	 * @param ClassProfile $profile
	 * @param mixed $instance
	 * @param string $property
	 * @throws \RuntimeException
	 */
	protected function getPropertyValue($profile, $instance, $property) {
		$attr = $profile->getProperty($property);
		
		if ($profile->getReflectionClass()->isInstance($instance)) {
			return $attr->getReflectionProperty()->getValue($instance);
		}
	
		$name = $attr->getName();
	
		if ($instance instanceof \stdClass) {
			return property_exists($instance, $name) ? $instance->$name : null;
		}
		elseif (is_array($instance) || $instance instanceof \ArrayObject) {
			return array_key_exists($name, $instance) ? $instance[$name] : null;
		}
		elseif (property_exists($instance, $name)) {
			$rc = new \ReflectionClass(get_class($instance));
			$alias = $rc->getProperty($name);
			$alias->setAccessible(true);
			return $alias->getValue($instance);
		}
		else {
			throw new \RuntimeException(sprintf("Property %s was not found on instance of class %s", $name, get_class($instance)));
		}
	}
	
	/**
	 * Obtains a class association value
	 * @param ClassProfile $profile
	 * @param mixed $instance
	 * @param Association $association
	 * @throws \RuntimeException
	 */
	protected function getAssociationValue($profile, $instance, $association) {
		if ($profile->getReflectionClass()->isInstance($instance)) {
			return $association->getReflectionProperty()->getValue($instance);
		}
		
		$name = $association->getName();
		
		if ($instance instanceof \stdClass) {
			return property_exists($instance, $name) ? $instance->$name : null;
		}
		elseif (is_array($instance) || $instance instanceof \ArrayObject) {
			return array_key_exists($name, $instance) ? $instance[$name] : null;
		}
		elseif (property_exists($instance, $name)) {
			$rc = new \ReflectionClass(get_class($instance));
			$alias = $rc->getProperty($name);
			$alias->setAccessible(true);
			return $alias->getValue($instance);
		}
		else {
			throw new \RuntimeException(sprintf("Property %s was not found on instance of class %s", $name, get_class($instance)));
		}
	}
}
?>