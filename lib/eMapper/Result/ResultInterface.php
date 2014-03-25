<?php
namespace eMapper\Result;

abstract class ResultInterface implements \Iterator {
	/**
	 * RESULT TYPES
	 */
	const AS_ARRAY  = 0;
	const AS_OBJECT = 1;
	
	/**
	 * Query result
	 * @var resource | object
	 */
	public $result;
	
	/**
	 * Internal counter
	 * @var int
	 */
	protected $counter;
	
	public function __construct($result) {
		$this->result = $result;
		$this->counter = 0;
	}
	
	/**
	 * Returns the amount of rows obtained
	 * @var int
	 */
	public abstract function countRows();
	
	/**
	 * Returns an associative array containing all column types by name
	 */
	public abstract function columnTypes($resultType = ArrayType::ASSOC);
	
	/**
	 * Fetchs a row to an array
	 * @param int $resultType
	 */
	public abstract function fetchArray($resultType = ArrayType::BOTH);
	
	/**
	 * Fetchs a row to an object
	 * @param string $className
	 */
	public abstract function fetchObject($className = null);
	
	/**
	 * ITERATOR METHODS
	 */
	
	public function valid() {
		return $this->counter != $this->countRows();
	}
	
	public function current($as = self::AS_ARRAY, $resultType = ArrayType::BOTH, $className = null) {
		if ($as == self::AS_ARRAY) {
			return $this->fetchArray($resultType);
		}
		
		return $this->fetchObject($className);
	}
	
	public function key() {
		return $this->counter;
	}
	
	public function next() {
		$this->counter++;
	}
	
	public function rewind() {
		$this->counter = 0;
	}
}
?>