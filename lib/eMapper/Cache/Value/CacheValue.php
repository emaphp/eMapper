<?php
namespace eMapper\Cache\Value;

/**
 * The CacheValue class is a value wrapper and its purpose is to define the structure of any value
 * that is stored in cache along with the data used for mapping.
 * @author emaphp
 */
class CacheValue {
	/**
	 * Value stored
	 * @var mixed
	 */
	protected $value;
	
	/**
	 * Data mapper class
	 * @var string
	 */
	protected $class;
	
	/**
	 * Mapper argument
	 * @var string
	 */
	protected $argument;
	
	/**
	 * Group keys
	 * @var array
	 */
	protected $groupKeys;
	
	/**
	 * Mapping method
	 * @var string
	 */
	protected $method;
	
	public function __construct($value, $class, $argument, $groupKeys, $method) {
		$this->value = $value;
		$this->class = $class;
		$this->argument = $argument;
		$this->method = $method;
		$this->groupKeys = $groupKeys;
	}
	
	/**
	 * Obtains wrapped data
	 * @return mixed
	 */
	public function getData() {
		return $this->data;
	}
	
	/**
	 * Obtains mapping class name
	 * @return string
	 */
	public function getClass() {
		return $this->class;
	}
	
	/**
	 * Obtains mapping class additional argument
	 * @return mixed
	 */
	public function getArgument() {
		return $this->argument;
	}
	
	/**
	 * Obtains the method used for mapping
	 * @return string
	 */
	public function getMethod() {
		return $this->method;
	}
	
	/**
	 * Obtains generated group names for this result
	 * @return array
	 */
	public function getGroupKeys() {
		return $this->groupKeys;
	}
	
	/**
	 * Obtains wrapped value
	 * @return \eMapper\Cache\Value\mixed
	 */
	public function getValue() {
		return $this->value;
	}
	
	/**
	 * Generates a mapping callback for the stored data
	 * @param TypeManager $typeManager
	 * @return array
	 */
	public function buildMappingCallback($typeManager) {
		$rc = new \ReflectionClass($this->class);
		
		if ($rc->isSubclassOf('eMapper\Result\Mapper\ComplexMapper')) {
			$mapper = $rc->newInstance($typeManager, $this->argument);
			$mapper->setGroupKeys($this->groupKeys);
		}
		else {
			$mapper = $rc->newInstance(new $this->argument);
		}
		
		return [$mapper, $this->method];
	}
}
?>