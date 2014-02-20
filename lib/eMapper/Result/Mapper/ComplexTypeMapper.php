<?php
namespace eMapper\Result\Mapper;

use eMapper\Type\TypeManager;

abstract class ComplexTypeMapper {
	/**
	 * Type manager
	 * @var TypeManager
	 */
	public $typeManager;
	
	/**
	 * Result map
	 * @var string
	 */
	public $resultMap;
	
	/**
	 * An array containing all column types
	 * @var array
	 */
	protected $columnTypes;
	
	/**
	 * Property list
	 * @var array
	 */
	protected $propertyList;
	
	/**
	 * Relation list
	 * @var array
	 */
	protected $relationList;
	
	/**
	 * Group indexes
	 * @var array
	 */
	public $groupKeys;
	
	/**
	 * Result map type handler list
	 * @var array
	 */
	protected $typeHandlers;
	
	public function __construct(TypeManager $typeManager, $resultMap = null) {
		$this->typeManager = $typeManager;
		$this->resultMap = $resultMap;
	}
	
	/**
	 * Obtains a column default type handler
	 * @param mixed $column
	 * @return TypeHandler | FALSE
	 */
	protected function columnHandler($column) {
		return $this->typeManager->getTypeHandler($this->columnTypes[$column]);
	}
	
	/**
	 * Builds a result map property list
	 * @throws \UnexpectedValueException
	 */
	protected abstract function validateResultMap();
	
	protected function validateIndex($index, $indexType) {		
		if (is_null($this->resultMap)) {
			//find index column on given result
			if (!array_key_exists($index, $this->columnTypes)) {
				if (is_numeric($index) && array_key_exists(intval($index), $this->columnTypes)) {
					$index = intval($index);
				}
				else {
					throw new \UnexpectedValueException("Index column '$index' not found");
				}
			}
				
			//get index type
			$indexColumn = $index;
			$indexType = is_null($indexType) ? $this->columnTypes[$index] : $indexType;
				
			//obtain index type handler
			$indexTypeHandler = $this->typeManager->getTypeHandler($indexType);
				
			if ($indexTypeHandler === false) {
				throw new \UnexpectedValueException("Unknown type '$indexType' defined for index '$index'");
			}
		}
		else {
			//find index property
			if (!array_key_exists($index, $this->propertyList)) {
				throw new \UnexpectedValueException("Index property '$index' was not found in result map");
			}
				
			//obtain index column and type handler
			$indexColumn = $this->propertyList[$index]->column;
			$indexTypeHandler = $this->typeHandlers[$index];
		}
		
		return [$indexColumn, $indexTypeHandler];
	}
	
	protected function validateGroup($group, $groupType) {
		if (is_null($this->resultMap)) {
			//find group column
			if (!array_key_exists($group, $this->columnTypes)) {
				if (is_numeric($group) && array_key_exists(intval($group), $this->columnTypes)) {
					$group = intval($group);
				}
				else {
					throw new \UnexpectedValueException("Group column '$group' not found");
				}
			}
		
			//get group type
			$groupType = is_null($groupType) ? $this->columnTypes[$group] : $groupType;
			$groupColumn = $group;
		
			//obtain group type handler
			$groupTypeHandler = $this->typeManager->getTypeHandler($groupType);
		
			if ($groupTypeHandler === false) {
				throw new \UnexpectedValueException("Unknown type '$groupType' defined for group '$group'");
			}
		}
		else {
			//find group property
			if (!array_key_exists($group, $this->propertyList)) {
				throw new \UnexpectedValueException("Group property '$group' was not found in result map");
			}
		
			//obtain group column and type handler
			$groupColumn = $this->propertyList[$group]->column;
			$groupTypeHandler = $this->typeHandlers[$group];
		}
		
		return [$groupColumn, $groupTypeHandler];
	}
	
	public abstract function relate(&$row, $parameterMap, $mapper);
}