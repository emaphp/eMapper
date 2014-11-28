<?php
namespace eMapper\SQL\Fluent;

use eMapper\Query\Column;

/**
 * The FluentInsert class provides a fluent interface for building INSERT queries
 * @author emaphp
 */
class FluentInsert extends AbstractFluentQuery {
	/**
	 * Value to insert
	 * @var array
	 */
	protected $value;
	
	/**
	 * List of values to insert
	 * @var array
	 */
	protected $valueList;
	
	/**
	 * Insert expression
	 * @var string
	 */
	protected $expression;
	
	/**
	 * List of columns
	 * @var array
	 */
	protected $columnList;
	
	/**
	 * Sets the values to insert as a list
	 * @param mixed $values
	 * @return \eMapper\SQL\Fluent\FluentInsert
	 */
	public function values($values) {
		$this->valueList = func_get_args();
		return $this;
	}
	
	/**
	 * Sets the VALUES clause along with its arguments
	 * @param string $expression
	 * @return \eMapper\SQL\Fluent\FluentInsert
	 */
	public function valuesExpr($expression) {
		$args = func_get_args();
		$this->expression = array_shift($args);
		
		if (empty($args))
			return $this;
		
		//check if argument is a list
		try {
			$this->valuesArray($args[0]);
		}
		catch (\InvalidArgumentException $e) {
			$this->valueList = $args;
		}
		
		return $this;
	}
	
	/**
	 * Sets the value to insert as an associative array
	 * @param mixed $values
	 * @throws \InvalidArgumentException
	 * @return \eMapper\SQL\Fluent\FluentInsert
	 */
	public function valuesArray($values) {
		if ($values instanceof \ArrayObject)
			$this->value = $values->getArrayCopy();
		elseif (is_object($values))
			$this->value = get_object_vars($values);
		elseif (is_array($values))
			$this->value = $values;
		else
			throw new \InvalidArgumentException("Method 'valuesArray' expected an object or array value");
		
		if (is_numeric(key($this->value))) {
			$this->valueList = $this->value;
			$this->value = null;
		}
		
		return $this;
	}
	
	/**
	 * Sets the column list
	 * @return \eMapper\SQL\Fluent\FluentInsert
	 */
	public function columns($columns) {
		$this->columnList = func_get_args();
		return $this;
	}
	
	/**
	 * Translates a insert column
	 * @param Column|string$column
	 * @return string
	 */
	protected function translateColumn($column) {
		if ($column instanceof Column)
			return $column->getName();
		
		return preg_match('/^(\w+)/', $column, $matches) ? $matches[1] : $column;
	}
	
	/**
	 * Returns the column list as a string
	 * @return string
	 */
	protected function buildColumnsClause() {
		if (empty($this->columnList)) {
			if (!empty($this->value))				
				$this->columnList = array_keys($this->value);
			else
				return '';
		}
		
		$columns = [];
		
		foreach ($this->columnList as $column)
			$columns[] = $this->translateColumn($column);
			
		return implode(',', $columns);
	}
	
	/**
	 * Returns the property to fetch along with its type (when specified)
	 * @param Column|string $column
	 * @return string
	 */
	protected function parseColumn($column) {
		if ($column instanceof Column) {
			$type = $column->getType();
			return empty($type) ? $column->getName() : $column->getName() . ':' . $type;
		}
		
		return $column;
	}
	
	/**
	 * Returns the value list expression
	 * @return string
	 */
	protected function buildValuesClause() {
		if (!empty($this->expression))
			return $this->expression;
		
		if (empty($this->value)) {
			$values = [];
			
			for ($i = 0, $n = count($this->valueList); $i < $n; $i++)
				$values[] = '%{' . $i . '}';
			
			return implode(',', $values);
		}
		

		$values = [];
		
		foreach ($this->columnList as $column) {
			$name = $this->parseColumn($column);
			$values[] = '#{' . $name . '}';
		}

		return implode(',', $values);
	}
	
	public function build() {
		$columns = $this->buildColumnsClause();
		$valueExpr = $this->buildValuesClause();
		$table = $this->fromClause->getTable();
		$query = empty($columns) ? "INSERT INTO $table VALUES ($valueExpr)" : "INSERT INTO $table ($columns) VALUES ($valueExpr)";
		return [$query, empty($this->value) ? $this->valueList : [$this->value]];
	}
}
?>