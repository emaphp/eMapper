<?php
namespace eMapper\Query\SQL;

use eMapper\Query\Field;
use eMapper\Query\Column;

class FluentTranslator implements ColumnTranslator {
	protected $tables;
	
	public function __construct(\ArrayObject $tables) {
		$this->tables = $tables;
	}
	
	public function translate(Field $column, \ArrayObject $joins = null, $alias = null) {
		if ($column instanceof Column) {
			$path = $column->getPath();
				
			if (empty($path))
				return empty($alias) ? $column->getName() : $alias . '.' . $column->getName();
				
			$references = $column->getPath()[0];
		
			if (!array_key_exists($references, $this->tables))
				throw new \UnexpectedValueException("Column {$column->getName()} references an unknown table '$references'");
		
			if (is_null($this->tables[$references]))
				return $references . '.' . $column->getName();
			else
				return $this->tables[$references] . '.' . $column->getName();
		}
		elseif (is_string($column) && !empty($column))
			return $column;
		else
			throw new \InvalidArgumentException("Columns must be specified either by a Column instance or a non-empty string");
	}
}
?>