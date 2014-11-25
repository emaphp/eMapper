<?php
namespace eMapper\SQL\Field;

use eMapper\Query\Field;
use eMapper\Query\Column;

class FluentFieldTranslator implements FieldTranslator {
	/**
	 * Joined tables
	 * @var array
	 */
	protected $tableList;
	
	public function __construct(array $tableList) {
		$this->tableList = $tableList;
	}
	
	public function translate(Field $column, $alias, &$joins = null) {
		if ($column instanceof Column) {
			$path = $column->getPath();
				
			if (empty($path))
				return empty($alias) ? $column->getName() : $alias . '.' . $column->getName();
				
			$references = $column->getPath()[0];
		
			if (!array_key_exists($references, $this->tableList))
				throw new \UnexpectedValueException("Column {$column->getName()} references an unknown table '$references'");
			
			return $references . '.' . $column->getName();
		}
		elseif (is_string($column) && !empty($column))
			return $column;
		else
			throw new \InvalidArgumentException("Columns must be specified either by a Column instance or a non-empty string");
	}
}
?>