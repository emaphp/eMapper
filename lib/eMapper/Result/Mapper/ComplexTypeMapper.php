<?php
namespace eMapper\Result\Mapper;

use eMapper\Type\TypeManager;
use eMapper\Reflection\Profiler;
use eMapper\Type\TypeHandler;
use eMapper\Result\Relation\MacroExpression;
use eMapper\Result\Relation\StatementCallback;
use eMapper\Result\Relation\QueryCallback;

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
	 * Parameter map
	 * @var string
	 */
	public $parameterMap;
	
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
	protected $groupKeys;
	
	public function __construct(TypeManager $typeManager, $resultMap = null, $parameterMap = null) {
		$this->typeManager = $typeManager;
		$this->resultMap = $resultMap;
		$this->parameterMap = $parameterMap;
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
	protected function validateResultMap() {
		//obtain property list
		$fields = Profiler::getClassProperties($this->resultMap);
		$this->propertyList = $this->relationList = array();
		
		foreach ($fields as $name => $field) {
			//parse relation annotations
			if ($field->has('eval')) {
				$this->relationList[$name] = new MacroExpression($field, $this->parameterMap);
			}
			elseif ($field->has('stmt')) {
				$this->relationList[$name] = new StatementCallback($field, $this->parameterMap);
			}
			elseif ($field->has('query')) {
				$this->relationList[$name] = new QueryCallback($field, $this->parameterMap);
			}
			elseif ($field->has('procedure')) {
				$this->relationList[$name] = new StatementCallback($field, $this->parameterMap);
			}
			else {
				//get column
				$column = $field->has('column') ? $field->get('column') : $name;
					
				if (!array_key_exists($column, $this->columnTypes)) {
					throw new \UnexpectedValueException("Column '$column' was not found on this result");
				}
					
				$this->propertyList[$name] = array('column' => $column);
					
				//get type handler
				if ($field->has('type')) {
					$type = $field->get('type');
					$typeHandler = $this->typeManager->getTypeHandler($type);
						
					if ($typeHandler === false) {
						throw new \UnexpectedValueException("No typehandler assigned to type '$type' defined at property $name");
					}
				
					$this->propertyList[$name]['handler'] = $typeHandler;
				}
				elseif ($field->has('var')) {
					//as 'var' is a common annotation no error is thrown if no associated type handler is found
					$type = $field->get('var');
					$typeHandler = $this->typeManager->getTypeHandler($type);
						
					if ($typeHandler !== false) {
						$this->propertyList[$name]['handler'] = $typeHandler;
					}
					else {
						echo $type;
						$this->propertyList[$name]['handler'] = $this->columnHandler($column);
					}
				}
				else {
					$this->propertyList[$name]['handler'] = $this->columnHandler($column);
				}
					
				//get setter method
				if ($field->has('setter')) {
					$this->propertyList[$name]['setter'] = $field->get('setter');
				}
			}
		}
		
		//validate setter methods and properties accesibility
		if ($this instanceof ObjectTypeMapper) {
			$reflectionClass = null;
			
			if (Profiler::isEntity($this->resultMap)) {
				$reflectionClass = Profiler::getReflectionClass($this->resultMap);
			}
			else {
				//obtain class from annotation
				$profile = Profiler::getClassAnnotations($this->resultMap);
				$defaultClass = $profile->has('defaultClass') ? $profile->get('defaultClass') : $this->defaultClass;
				
				if ($defaultClass != 'stdClass' && $defaultClass != 'ArrayObject') {
					$reflectionClass = Profiler::getReflectionClass($defaultClass);
				}
			}
			
			if (isset($reflectionClass)) {
				foreach ($this->propertyList as $name => $props) {
					if (array_key_exists('setter', $props)) {
						$setter = $props['setter'];
						
						//validate setter method
						if (!$reflectionClass->hasMethod($setter)) {
							throw new \UnexpectedValueException(sprintf("Setter method $setter not found in class %s", $reflectionClass->getName()));
						}
							
						$method = $reflectionClass->getMethod($setter);
							
						if (!$method->isPublic()) {
							throw new \UnexpectedValueException(sprintf("Setter method $setter does not have public access in class %s", $reflectionClass->getName()));
						}
					}
					else {
						//validate property
						if (!$reflectionClass->hasProperty($name)) {
							throw new \UnexpectedValueException(sprintf("Unknown property $name in class %s", $reflectionClass->getName()));
						}
							
						$property = $reflectionClass->getProperty($name);
							
						if (!$property->isPublic()) {
							throw new \UnexpectedValueException(sprintf("Property $name does not public access in class %s", $reflectionClass->getName()));
						}
					}
				}
			}
		}
	}
	
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
			$indexColumn = $this->propertyList[$index]['column'];
			$indexTypeHandler = $this->propertyList[$index]['handler'];
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
			$groupColumn = $this->propertyList[$group]['column'];
			$groupTypeHandler = $this->propertyList[$group]['handler'];
		}
		
		return [$groupColumn, $groupTypeHandler];
	}
	
	public abstract function relate(&$row, $mapper);
}