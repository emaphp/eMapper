<?php
namespace eMapper\Cache\Value;

/**
 * The CacheValue class is a value wrapper and its purpose is to define the structure of any value
 * that is stored in cache.
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
	 * Maping method
	 * @var string
	 */
	protected $method;
	
	public function __construct($data, $class, $argument, $groupKeys, $method) {
		$this->data = $data;
		$this->class = $class;
		$this->argument = $argument;
		$this->method = $method;
		$this->groupKeys = $groupKeys;
	}
	
	public function getData() {
		return $this->data;
	}
	
	public function getClass() {
		return $this->class;
	}
	
	public function getArgument() {
		return $this->argument;
	}
	
	public function getMethod() {
		return $this->method;
	}
	
	public function getGroupKeys() {
		return $this->groupKeys;
	}
	
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