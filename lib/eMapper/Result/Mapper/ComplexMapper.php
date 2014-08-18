<?php
namespace eMapper\Result\Mapper;

use eMapper\Type\TypeManager;
use eMapper\Reflection\Profiler;

/**
 * The ComplexMapper class provides common logic for array an object mappers.
 * @author emaphp
 */
abstract class ComplexMapper {
	/**
	 * Type manager
	 * @var TypeManager
	 */
	protected $typeManager;
	
	/**
	 * Result map
	 * @var ClassProfile
	 */
	protected $resultMap;
		
	/**
	 * Result map properties (PROPERTY => PROFILE)
	 * @var array
	 */
	protected $properties;
	
	/**
	 * An array containing all column types handlers (COLUMN => HANDLER)
	 * @var array
	 */
	protected $columnTypes;
	
	/**
	 * An array containing all available columns in a result (PROPERTY => COLUMN)
	 * @var array
	 */
	protected $availableColumns = [];

	/**
	 * Result map type handler list (PROPERTY => HANDLER)
	 * @var array
	 */
	protected $typeHandlers = [];
	
	/**
	 * Group indexes
	 * @var array
	 */
	protected $groupKeys = [];
	
	public function __construct(TypeManager $typeManager, $resultMap = null) {
		$this->typeManager = $typeManager;
		
		if (!is_null($resultMap)) {
			$this->resultMap = Profiler::getClassProfile($resultMap);
			$this->properties = $this->resultMap->getProperties();
		}
	}
	
	/**
	 * Obtains a column default type handler
	 * @param mixed $column
	 * @return TypeHandler | FALSE
	 */
	protected function getColumnHandler($column) {
		return $this->typeManager->getTypeHandler($this->columnTypes[$column]);
	}
	
	/**
	 * Validates a user defined index againts current result
	 * @param string $index
	 * @param string $indexType
	 * @throws \UnexpectedValueException
	 * @return array
	 */
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
			if (!array_key_exists($index, $this->properties)) {
				throw new \UnexpectedValueException("Index property '$index' was not found in result map");
			}
				
			//obtain index column and type handler
			$indexColumn = $this->properties[$index]->getColumn();
			$indexTypeHandler = $this->typeHandlers[$index];
		}
		
		return [$indexColumn, $indexTypeHandler];
	}
	
	/**
	 * Validates a user defined group against current result
	 * @param string $group
	 * @param string $groupType
	 * @throws \UnexpectedValueException
	 * @return array
	 */
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
			if (!array_key_exists($group, $this->properties)) {
				throw new \UnexpectedValueException("Group property '$group' was not found in result map");
			}
		
			//obtain group column and type handler
			$groupColumn = $this->properties[$group]->getColumn();
			$groupTypeHandler = $this->typeHandlers[$group];
		}
		
		return [$groupColumn, $groupTypeHandler];
	}
	
	/**
	 * Determines if mapper has generated group keys
	 * @return boolean
	 */
	public function hasGroupKeys() {
		return !empty($this->groupKeys);
	}
	
	/**
	 * Obtains all group keys
	 * @return array
	 */
	public function getGroupKeys() {
		return $this->groupKeys;
	}
	
	/**
	 * Sets mapper group keys
	 * @param array $groupKeys
	 */
	public function setGroupKeys($groupKeys) {
		$this->groupKeys = $groupKeys;
	}
	
	/**
	 * Builds a list of type handlers for the available columns
	 * @throws \UnexpectedValueException
	 */
	protected function buildTypeHandlerList() {
		foreach ($this->properties as $name => $propertyProfile) {
			$column = $propertyProfile->getColumn();
			
			if (!array_key_exists($column, $this->columnTypes)) {
				continue;
			}
			
			$this->availableColumns[$name] = $column;
			$type = $propertyProfile->getType();
				
			if (isset($type)) {
				$typeHandler = $this->typeManager->getTypeHandler($type);
	
				if ($typeHandler == false) {
					throw new \UnexpectedValueException("No typehandler assigned to type '$type' defined at property $name");
				}
	
				$this->typeHandlers[$name] = $typeHandler;
			}
			else {
				$type = $this->columnTypes[$column];
				$this->typeHandlers[$name] = $this->typeManager->getTypeHandler($type);
			}
		}
	}

	/**
	 * Sets the mapper type manager
	 * @param TypeManager $typeManager
	 */
	public function setTypeManager($typeManager) {
		$this->typeManager = $typeManager;
	}
	
	/**
	 * Obtains the current result map
	 * @return string
	 */
	public function getResultMap() {
		return $this->resultMap;
	}
	
	/**
	 * Evaluates first order attributes for a given row
	 * @param mixed $row
	 * @param Mapper $mapper
	 */
	public abstract function evaluateFirstOrderAttributes(&$row, $mapper);
	
	/**
	 * Evaluates second order attributes for a given row
	 * @param mixed $row
	 * @param Mapper $mapper
	 */
	public abstract function evaluateSecondOrderAttributes(&$row, $mapper);
	
	/**
	 * Evaluates class associations
	 * @param mixed $row
	 * @param Mapper $mapper
	 */
	public abstract function evaluateAssociations(&$row, $mapper);
}