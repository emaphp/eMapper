<?php
namespace eMapper\Result\Mapper;

/**
 * The ArrayObjectMapper class maps rows to ArrayObject instances.
 * @author emaphp
 */
class ArrayObjectMapper extends ArrayMapper {
	protected function map($row) {
		$result = new \ArrayObject([]);
	
		if (is_null($this->resultMap)) {
			foreach ($row as $column => $value) {
				$typeHandler = $this->getColumnHandler($column);
				$result[$column] = is_null($row[$column]) ? null : $typeHandler->getValue($value);
			}
		}
		else {
			foreach ($this->properties as $name => $propertyProfile) {
				$column = $propertyProfile->getColumn();
				$typeHandler = $this->typeHandlers[$name];
				$result[$name] = is_null($row[$column]) ? null : $typeHandler->getValue($row[$column]);
			}
		}
	
		return $result;
	}
}
?>