<?php
namespace eMapper\Result;

/**
 * The ResultIterator class defines a basic iterator for database results.
 * @author emaphp
 */
abstract class ResultIterator implements \Iterator {
	/*
	 * RESULT TYPES
	 */
	const AS_ARRAY  = 0;
	const AS_OBJECT = 1;
	
	/**
	 * Query result
	 * @var resource | object
	 */
	protected $result;
	
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
	 * Obtains current result
	 * @return resource
	 */
	public function getResult() {
		return $this->result;
	}
	
	/**
	 * Returns the amount of rows obtained
	 * @var int
	 */
	public abstract function countRows();
	
	/**
	 * Returns an associative array containing all column types by name
	 */
	public abstract function getColumnTypes($resultType = ArrayType::ASSOC);
	
	/**
	 * Fetchs a row to an array
	 * @param int $resultType
	 */
	public abstract function fetchArray($resultType = ArrayType::BOTH);
	
	/**
	 * Fetchs a row to an object
	 */
	public abstract function fetchObject();
	
	/**
	 * Frees a result
	 */
	public abstract function free();
	
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