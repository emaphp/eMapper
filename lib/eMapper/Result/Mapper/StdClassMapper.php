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
				$result->$property = is_null($row[$column]) ? null : $typeHandler->getValue($row[$column]);
			}
		}
	
		return $result;
	}
	
	public function evaluateFirstOrderAttributes(&$row, $mapper) {
		foreach ($this->resultMap->getFirstOrderAttributes() as $name => $attribute) {
			$row->$name = $attribute->evaluate($row, $mapper);
		}
	}
	
	public function evaluateSecondOrderAttributes(&$row, $mapper) {
		if ($mapper->getOption('depth.current') < $mapper->getOption('depth.limit')) {
			foreach ($this->resultMap->getSecondOrderAttributes() as $name => $attribute) {
				$row->$name = $attribute->evaluate($row, $mapper);
			}
		}
		else {
			foreach (array_keys($this->resultMap->getSecondOrderAttributes()) as $name) {
				$row->$name = null;
			}
		}
	}
}
?>