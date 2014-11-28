<?php
namespace eMapper\SQL\Fluent;

use eMapper\SQL\Predicate\SQLPredicate;
use eMapper\SQL\Field\FluentFieldTranslator;

/**
 * The FluentUpdate class provides a fluent interface for building UPDATE queries
 * @author emaphp
 */
class FluentUpdate extends AbstractFluentQuery {
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
		//FROM clause
		$from = rtrim($this->fromClause->build());
		$fromArgs = $this->fromClause->getArguments();
		
		//create field translator from joined tables
		$this->translator = new FluentFieldTranslator($this->fromClause->getTableList());
		
		//WHERE clause
		$where = rtrim($this->buildWhereClause());
	
		//SET clause
		$set = $this->buildSetClause();

		//build query structure
		$query = empty($where) ? "UPDATE $from SET $set" : "UPDATE $from SET $set WHERE $where";
		
		//generate query arguments
		$args = [];
		$counter = 0;
		$complexArg = !empty($fromArgs) ? $fromArgs : [];

		//append arguments from SET clause
		if (isset($this->expression)) {
			foreach ($this->valueList as $arg)
				$args[$counter++] = $arg;
		}
		else
			$complexArg = array_merge($complexArg, $this->value);
		
		//append arguments in WHERE clause
		if (isset($this->whereClause)) {
			$whereArgs = $this->whereClause->getArguments();
			
			if ($this->whereClause->getClause() instanceof SQLPredicate)
				$complexArg = array_merge($whereArgs, $complexArg);
			elseif (!empty($whereArgs)) {
				foreach ($whereArgs as $arg)
					$args[$counter++] = $arg;
			}
		}
		
		//append complexArg to argument list if necessary
		if (!empty($complexArg))
			array_unshift($args, $complexArg);
		
		return [$query, $args];
	}
}
?>