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
			
			//parse relation annotations
			if ($field->has('eval')) {
				$this->relationList[$name] = new MacroExpression($field, $this->parameterMap);
			}
			elseif ($field->has('stmt')) {
				$this->relationList[$name] = new StatementCallback($field);
			}
			elseif ($field->has('query')) {
				$this->relationList[$name] = new QueryCallback($field);
			}
			elseif ($field->has('procedure')) {
				$this->relationList[$name] = new StatementCallback($field);
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
	
	public abstract function relate(&$row, $mapper);
}