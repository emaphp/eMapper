<?php
namespace eMapper\Result\Mapper;

use eMapper\Type\TypeHandler;
use eMapper\Result\ResultIterator;
use eMapper\Result\ArrayType;

/**
 * The ScalarTypeMapper class maps a result to scalar types.
 * @author emaphp
 */
class ScalarTypeMapper {
	/**
	 * Default type handler
	 * @var TypeHandler
	 */
	protected $typeHandler;
	
	public function __construct(TypeHandler $typeHandler) {
		$this->typeHandler = $typeHandler;
	}

	public function mapResult(ResultIterator $result, $column = null) {
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
	
	public function mapList(ResultIterator $result, $column = null) {
		if ($result->countRows() == 0) {
			return [];
		}
		
		$list = [];
		
		if (is_null($column) || empty($column)) {
			$column = 0;
		}
		elseif (!is_integer($column) && !is_string($column)) {
			throw new \InvalidArgumentException("Column must be defined as a string or integer");
		}
		
		while ($result->valid()) {
			$row = $result->fetchArray(ArrayType::BOTH);
			$list[] = (is_null($row[$column])) ? null : $this->typeHandler->getValue($row[$column]);
			$result->next();
		}
		
		return $list;
	}
}
?>