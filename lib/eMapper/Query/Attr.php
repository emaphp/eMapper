<?php
namespace eMapper\Query;

use eMapper\Reflection\Profile\ClassProfile;

/**
 * The Attr class represents an entity attribute.
 * @author emaphp
 */
class Attr extends Field {
	/**
	 * Attribute path
	 * @var array
	 */
	protected $path;
	
	public function __construct($name, $type = null) {
		if (strstr($name, '__')) {
			$this->path = explode('__', $name);
			$this->name = array_pop($this->path);
		}
		else {
			$this->name = $name;
		}
		 
		$this->type = $type;
	}
	
	/**
	 * Returns a new Attr instance
	 * @param string $method
	 * @param array $args
	 * @return \eMapper\Query\Attr
	 */
	public static function __callstatic($method, $args = null) {
		if (!empty($args)) {
			return new Attr($method, $args[0]);
		}
		
		return new Attr($method);
	}
	
	public function getColumnName(ClassProfile $profile) {
		if (is_null($this->path)) {
			$propertyNames = $profile->getPropertyNames();
			
			if (!array_key_exists($this->name, $propertyNames)) {
				throw new \RuntimeException(sprintf("Attribute {$this->name} not found in class %s", $profile->getReflectionClass()->getName()));
			}
			
			return $propertyNames[$this->name];
		}
		
		$current = $profile;
		
		foreach ($this->path as $property) {
			$association = $current->getAssociation($property);
			
			if ($association === false) {
				throw new \RuntimeException(sprintf("Association '%s' not found in class %s", $property, $current->getReflectionClass()->getName()));
			}
			
			$current = $association->getProfile();
		}
		
		$propertyNames = $current->getPropertyNames();
			
		if (!array_key_exists($this->name, $propertyNames)) {
			throw new \RuntimeException(sprintf("Attribute {$this->name} not found in class %s", $current->getReflectionClass()->getName()));
		}
			
		return $propertyNames[$this->name];
	}
	
	public function getFullPath() {
		if (is_null($this->path)) {
			return null;
		}
		
		return implode('_', $this->path);
	}
	
	public function getAssociations(ClassProfile $profile, $return_profile = true) {
		if (is_null($this->path)) {
			if ($return_profile) {
				return [null, null];
			}
			
			return null;
		}
		
		$associations = [];
		$current = $profile;
		
		for ($i = 0; $i < count($this->path); $i++) {
			//build name
			$name = implode('_', array_slice($this->path, 0, $i + 1));
			
			$property = $this->path[$i];
			$association = $current->getAssociation($property);
				
			if ($association === false) {
				throw new \RuntimeException(sprintf("Association '%s' not found in class %s", $property, $current->getReflectionClass()->getName()));
			}
			
			$associations[$name] = $association;
			$current = $association->getProfile();
		}
		
		if ($return_profile) {
			return [$associations, $current];
		}
		
		return $associations;
	}
}
?>