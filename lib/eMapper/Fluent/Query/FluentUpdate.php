<?php
namespace eMapper\Fluent\Query;

use eMapper\SQL\Predicate\SQLPredicate;
use eMapper\Query\Schema;

/**
 * The FluentUpdate class provides a fluent interface for building UPDATE queries
 * @author emaphp
 */
class FluentUpdate extends AbstractQuery {
	/**
	 * Values to update as a hash table
	 * @var array
	 */
	protected $value = [];
	
	/**
	 * SET clause expression
	 * @var string
	 */
	protected $expression;
	
	/**
	 * Values to update as a list
	 * @var array
	 */
	protected $valueList = [];
	
	/**
	 * Sets a field value
	 * @param string $fieldName
	 * @param mixed $value
	 * @return \eMapper\SQL\Fluent\FluentUpdate
	 */
	public function set($fieldName, $value) {
		$this->value[$fieldName] = $value;
		return $this;
	}
	
	/**
	 * Sets the value to update as an array|object
	 * @param array|object $value
	 * @throws \InvalidArgumentException
	 * @return \eMapper\SQL\Fluent\FluentUpdate
	 */
	public function setValue($value) {
		if ($value instanceof \ArrayObject)
			$this->value = $value->getArrayCopy();
		elseif (is_object($value))
			$this->value = get_object_vars($value);
		elseif (is_array($value))
			$this->value = $value;
		else
			throw new \InvalidArgumentException("Method 'setValue' expected an object or array value");
		
		return $this;
	}
	
	/**
	 * Sets the value list expression
	 * @param string $expression
	 * @return \eMapper\SQL\Fluent\FluentUpdate
	 */
	public function setExpr($expression) {
		$this->valueList = func_get_args();
		$this->expression = array_shift($this->valueList);
		return $this;
	}
	
	/**
	 * Returns the set expression as a string
	 * @return string
	 */
	protected function buildSetClause() {
		if (isset($this->expression))
			return $this->expression;
		
		$set = [];
		
		foreach (array_keys($this->value) as $k )
			$set[] = $k . '=#{' . $k . '}';
		
		return implode(',', $set);
	}
	
	public function build() {
		//create query schema
		$schema = new Schema($this->getFluent()->getEntityProfile());
		
		//WHERE clause
		$where = null;
		if (isset($this->whereClause))
			$where = $this->whereClause->build($schema);
		
		//update schema
		$this->updateSchema($schema);
		
		//FROM clause
		$from = rtrim($this->fromClause->build($this, $schema));
		
		//SET clause
		$set = $this->buildSetClause();
		
		//build query
		$query = empty($where) ? "UPDATE $from SET $set" : "UPDATE $from SET $set WHERE $where";
		
		//generate query arguments
		$args = $complexArg = [];
		
		if (isset($this->expression))
			$args = $this->valueList;
		else
			$complexArg = $this->value;
		
		//obtain arguments in WHERE clause
		if (isset($this->whereClause) && $this->whereClause->hasArguments())
			$args = array_merge($args, $this->whereClause->getArguments());
			
		//get generated arguments
		if ($schema->hasArguments())
			$complexArg = array_merge($complexArg, $schema->getArguments());
		
		//append complexArg to argument list if necessary
		if (!empty($complexArg))
			array_unshift($args, $complexArg);
		
		return [$query, $args];
	}
}