<?php
namespace eMapper\Result\Mapper;

use eMapper\Type\TypeHandler;
use eMapper\Result\ResultInterface;

class ScalarTypeMapper {
	/**
	 * Default type handler
	 * @var TypeHandler
	 */
	protected $typeHandler;
	
	public function __construct(TypeHandler $typeHandler) {
		$this->typeHandler = $typeHandler;
	}

	public function mapResult(ResultInterface $result, $column = null) {
		if ($result->countRows() == 0) {
			return null;
		}
		
		if (empty($column)) {
			$column = 0;
		}
		elseif (!is_integer($column) && !is_string($column)) {
			throw new \InvalidArgumentException("Column must be defined as a string or integer");
		}
		
		$row = $result->fetchArray();
		return is_null($row[$column]) ? null : $this->typeHandler->getValue($row[$column]);
	}
	
	public function mapList(ResultInterface $result, $column = null) {
		if ($result->countRows() == 0) {
			return array();
		}
		
		$list = array();
		
		if (is_null($column) || empty($column)) {
			$column = 0;
		}
		elseif (!is_integer($column) && !is_string($column)) {
			throw new \InvalidArgumentException("Column must be defined as a string or integer");
		}
		
		while ($result->valid()) {
			$row = $result->fetchArray(ResultInterface::BOTH);
			$list[] = (is_null($row[$column])) ? null : $this->typeHandler->getValue($row[$column]);
			$result->next();
		}
		
		return $list;
	}
}
?>