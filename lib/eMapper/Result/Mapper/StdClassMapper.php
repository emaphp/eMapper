<?php
namespace eMapper\Result\Mapper;

use eMapper\Type\TypeManager;

class StdClassMapper extends ObjectMapper {
	public function __construct(TypeManager $typeManager, $resultMap = null) {
		ComplexMapper::__construct($typeManager, $resultMap);
		$this->defaultClass = 'stdClass';
	}
	
	protected function map($row) {
		$result = new \stdClass();
	
		if (is_null($this->resultMap)) {
			foreach ($row as $column => $value) {
				$typeHandler = $this->getColumnHandler($column);
				$result->$column = is_null($row->$column) ? null : $typeHandler->getValue($value);
			}
		}
		else {
			foreach ($this->availableColumns as $property => $column) {
				$typeHandler = $this->typeHandlers[$property];
				$result->$property = is_null($row->$column) ? null : $typeHandler->getValue($row->$column);
			}
		}
	
		return $result;
	}
	
	public function evaluateFirstOrderAttributes(&$row, $mapper) {
		foreach ($this->resultMap->getFirstOrderAttributes() as $name => $attribute)
			$row->$name = $attribute->evaluate($row, $mapper);
	}
	
	public function evaluateSecondOrderAttributes(&$row, $mapper) {
		foreach ($this->resultMap->getSecondOrderAttributes() as $name => $attribute)
			$row->$name = $attribute->evaluate($row, $mapper);
	}
	
	public function evaluateAssociations(&$row, $mapper) {
		foreach ($this->resultMap->getAssociations() as $name => $association)
			$row->$name = $association->evaluate($row, $mapper);
	}
}
?>