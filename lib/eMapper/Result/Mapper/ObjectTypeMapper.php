<?php
namespace eMapper\Result\Mapper;

use eMapper\Type\TypeManager;
use eMapper\Result\ResultInterface;
use eMapper\Reflection\Profiler;

class ObjectTypeMapper extends ComplexTypeMapper {
	/**
	 * Default conversion class
	 * @var string
	 */
	protected $defaultClass;
	
	/**
	 * Stores associated type handler for each column
	 * @var array
	 */
	protected $columns;
	
	/**
	 * ObjectMapper constructor
	 * @param TypeManager $typeManager
	 * @param string $resultMap
	 * @param string $defaultClass
	 */
	public function __construct(TypeManager $typeManager, $resultMap = null, $defaultClass = 'stdClass') {
		parent::__construct($typeManager, $resultMap);
		$this->defaultClass = $defaultClass;
	}
	
	protected function validateResultMap() {
		//get relations
		$this->relationList = Profiler::getClassProfile($this->resultMap)->dynamicAttributes;
		
		//obtain mapped properties
		$this->propertyList = Profiler::getClassProfile($this->resultMap)->propertiesConfig;
		
		//store type handlers while on it
		$this->typeHandlers = array();
		
		//get reflection class in order to validate setter methods
		$reflectionClass = ($this->defaultClass != 'stdClass' && $this->defaultClass != 'ArrayObject') ? Profiler::getClassProfile($this->defaultClass)->reflectionClass : null;		
		
		foreach ($this->propertyList as $name => $config) {
			//validate column
			if (!array_key_exists($config->column, $this->columnTypes)) {
				throw new \UnexpectedValueException("Column '{$config->column}' was not found on this result");
			}
			
			if (isset($reflectionClass)) {
				if (isset($config->setter)) {
					if (!$reflectionClass->hasMethod($config->setter)) {
						throw new \UnexpectedValueException("Setter method '$config->setter' was not found in class {$this->defaultClass}");
					}
				
					$reflectionMethod = $reflectionClass->getMethod($config->setter);
				
					if (!$reflectionMethod->isPublic()) {
						throw new \UnexpectedValueException("Setter method '$config->setter' is not public in class {$this->defaultClass}");
					}
				}
				elseif ($this->resultMap != $this->defaultClass) {
					if (!$reflectionClass->hasProperty($name)) {
						throw new \UnexpectedValueException("Property '$name' was not found in class {$this->defaultClass}");
					}
				
					$reflectionProperty = $reflectionClass->getProperty($name);
				
					if (!$reflectionProperty->isPublic()) {
						throw new \UnexpectedValueException("Property '$name' is not public in class {$this->defaultClass}");
					}
				}
			}
			
			//validate type
			if (isset($config->type)) {
				$typeHandler = $this->typeManager->getTypeHandler($config->type);
		
				if ($typeHandler == false) {
					throw new \UnexpectedValueException("No typehandler assigned to type '{$config->type}' defined at property $name");
				}
		
				$this->typeHandlers[$name] = $typeHandler;
			}
			elseif (isset($config->suggestedType)) {
				$typeHandler = $this->typeManager->getTypeHandler($config->suggestedType);
		
				if ($typeHandler == false) {
					$type = $this->columnTypes[$config->column];
					$this->typeHandlers[$name] = $this->typeManager->getTypeHandler($type);
				}
				else {
					$this->typeHandlers[$name] = $typeHandler;
				}
			}
			else {
				$type = $this->columnTypes[$config->column];
				$this->typeHandlers[$name] = $this->typeManager->getTypeHandler($type);
			}
		}
	}
	
	protected function validateColumns() {
		//obtain available columns
		$this->columns = array();
		$reflectionClass = Profiler::getClassProfile($this->defaultClass)->reflectionClass;
			
		//store type handlers while on it
		foreach (array_keys($this->columnTypes) as $column) {
			if (!$reflectionClass->hasProperty($column)) {
				continue;
			}
				
			//validate property
			$property = $reflectionClass->getProperty($column);
				
			if (!$property->isPublic()) {
				throw new \UnexpectedValueException(sprintf("Property %s in class %s has not public access", $property->getName(), $this->defaultClass));
			}
				
			$this->columns[$column] = $this->columnHandler($column);
		}
	}
	
	protected function map($row) {
		if (isset($this->resultMap)) {
			//create instance			
			$reflectionClass = Profiler::getClassProfile($this->defaultClass)->reflectionClass;
			$instance = $reflectionClass->newInstance();
			
			foreach ($this->propertyList as $name => $config) {
				$column = $config->column;
				$typeHandler = $this->typeHandlers[$name];
				
				if ($instance instanceof \stdClass) {
					$instance->$name = $typeHandler->getValue($row->$column);
				}
				elseif ($instance instanceof \ArrayObject) {
					$instance[$name] = $typeHandler->getValue($row->$column);
				}
				elseif (isset($config->setter)) {
					$setter = $config->setter;
					$instance->$setter($typeHandler->getValue($row->$column));
				}
				else {
					$instance->$name = $typeHandler->getValue($row->$column);
				}
			}
		}
		else {
			$instance = new $this->defaultClass;
			
			if ($this->defaultClass == 'stdClass') {
				foreach ($this->columnTypes as $column => $type) {
					if (is_integer($column)) {
						continue;
					}
					
					$typeHandler = $this->columnHandler($column);
					$instance->$column = $typeHandler->getValue($row->$column);
				}
			}
			elseif ($this->defaultClass == 'ArrayObject') {
				foreach ($this->columnTypes as $column => $type) {
					$typeHandler = $this->columnHandler($column);
					$instance[$column] = $typeHandler->getValue($row->$column);
				}
			}
			else {				
				foreach ($this->columns as $column => $typeHandler) {
					//set values
					$instance->$column = $typeHandler->getValue($row->$column);
				}
			}
		}
		
		return $instance;
	}
	
	/**
	 * Returns a mapped object from a mysqli_result object
	 * @param ResultInterface $result
	 * @return NULL|object
	 */
	public function mapResult(ResultInterface $result) {
		//check numer of rows returned
		if ($result->countRows() == 0) {
			return null;
		}
	
		//get result column types
		$this->columnTypes = $result->columnTypes();
	
		//validate result map (if any)
		if (isset($this->resultMap)) {
			$this->validateResultMap();
		}
		else {
			$this->validateColumns();
		}
		
		//get row as an object and map using its model
		return $this->map($result->fetchObject());
	}
	
	/**
	 * Returns a list of objects from a mysqli_result object
	 * @param ResultInterface $result
	 * @param string $index
	 * @param string $indexType
	 * @param string $group
	 * @param string $groupType
	 * @throws MySQLMapperException
	 * @return NULL|array
	 */
	public function mapList(ResultInterface $result, $index = null, $indexType = null, $group = null, $groupType = null) {
		//check numer of rows returned
		if ($result->countRows() == 0) {
			return array();
		}
	
		//get result column types
		$this->columnTypes = $result->columnTypes();
	
		//validate result map (if any)
		if (isset($this->resultMap)) {
			$this->validateResultMap();
		}
		else {
			$this->validateColumns();
		}
		
		$list = array();
		
		if (isset($index) || isset($group)) {
			//validate index column
			if (isset($index) && $indexType != 'callable') {
				list($indexColumn, $indexTypeHandler) = $this->validateIndex($index, $indexType);
			}
			
			//validate group
			if (isset($group) && $groupType != 'callable') {
				list($groupColumn, $groupTypeHandler) = $this->validateGroup($group, $groupType);
			}
			
			if (isset($index) && isset($group)) {
				$this->groupKeys = array();
				
				while ($result->valid()) {
					///get row
					$row = $result->fetchObject();
					$mappedRow = $this->map($row);
					
					if ($groupType == 'callable') {
						$key = call_user_func($group, $mappedRow);
						
						if (is_null($key)) {
							throw new \UnexpectedValueException("Group callback returned a NULL value");
						}
						elseif (!is_int($key) && !is_string($key)) {
							throw new \UnexpectedValueException("Group callback returned a value that is neither an integer or string");
						}
					}
					else {
						//validate group value
						$key = $row->$groupColumn;
						
						if (is_null($key)) {
							throw new \UnexpectedValueException("Null value found when grouping by column '$groupColumn'");
						}
							
						//obtain group value
						$key = $groupTypeHandler->getValue($key);
						
						if (!is_int($key) && !is_string($key)) {
							throw new \UnexpectedValueException("Obtained group key in column '$groupColumn' is neither an integer or string");
						}
					}
						
					if ($indexType == 'callable') {
						$idx = call_user_func($index, $mappedRow);
						
						if (is_null($idx)) {
							throw new \UnexpectedValueException("Index callback returned a NULL value");
						}
						elseif (!is_int($idx) && !is_string($idx)) {
							throw new \UnexpectedValueException("Index callback returned a value that is neither an integer or string");
						}
					}
					else {
						//validate index value
						$idx = $row->$indexColumn;
						
						if (is_null($idx)) {
							throw new \UnexpectedValueException("Null value found when indexing by column '$indexColumn'");
						}
						
						//obtain index value
						$idx = $indexTypeHandler->getValue($idx);
						
						if (!is_int($idx) && !is_string($idx)) {
							throw new \UnexpectedValueException("Obtained index key in column '$indexColumn' is neither an integer or string");
						}
					}
						
					//store value
					if (isset($list[$key])) {
						$list[$key][$idx] = $mappedRow;
					}
					else {
						$list[$key] = [$idx => $mappedRow];
						$this->groupKeys[] = $key;
					}
					
					$result->next();
				}
			}
			elseif (isset($index)) {
				while ($result->valid()) {
					///get row
					$row = $result->fetchObject();
					$mappedRow = $this->map($row);
					
					if ($indexType == 'callable') {
						$idx = call_user_func($index, $mappedRow);
						
						if (is_null($idx)) {
							throw new \UnexpectedValueException("Index callback returned a NULL value");
						}
						elseif (!is_int($idx) && !is_string($idx)) {
							throw new \UnexpectedValueException("Index callback returned a value that is neither an integer or string");
						}
					}
					else {
						//validate index value
						$idx = $row->$indexColumn;
						
						if (is_null($idx)) {
							throw new \UnexpectedValueException("Null value found when indexing by column '$indexColumn'");
						}
						
						//obtain index value
						$idx = $indexTypeHandler->getValue($idx);

						if (!is_int($idx) && !is_string($idx)) {
							throw new \UnexpectedValueException("Obtained index key in column '$indexColumn' is neither an integer or string");
						}
					}
					
					//store value and get next one
					$list[$idx] = $mappedRow;
					$result->next();
				}
			}
			else {
				$this->groupKeys = array();
				
				while ($result->valid()) {
					///get row
					$row = $result->fetchObject();
					$mappedRow = $this->map($row);
					
					if ($groupType == 'callable') {
						$key = call_user_func($group, $mappedRow);
						
						if (is_null($key)) {
							throw new \UnexpectedValueException("Group callback returned a NULL value");
						}
						elseif (!is_int($key) && !is_string($key)) {
							throw new \UnexpectedValueException("Group callback returned a value that is neither an integer or string");
						}
						
						//store value
						if (isset($list[$key])) {
							$list[$key][] = $row;
						}
						else {
							$list[$key] = [$row];
							$this->groupKeys[] = $key;
						}
					}
					else {
						//validate group value
						$key = $row->$groupColumn;
						
						if (is_null($key)) {
							throw new \UnexpectedValueException("Null value found when grouping by column '$groupColumn'");
						}
							
						//obtain group value
						$key = $groupTypeHandler->getValue($key);
						
						if (!is_int($key) && !is_string($key)) {
							throw new \UnexpectedValueException("Obtained group key in column '$groupColumn' is neither an integer or string");
						}
						
						//store value
						if (isset($list[$key])) {
							$list[$key][] = $this->map($row);
						}
						else {
							$list[$key] = [$this->map($row)];
							$this->groupKeys[] = $key;
						}
					}
					
					$result->next();
				}
			}
		}
		else {
			while ($result->valid()) {
				$list[] = $this->map($result->fetchObject());
				$result->next();
			}
		}
	
		return $list;
	}
	
	public function relate(&$row, $parameterMap, $mapper) {
		foreach ($this->relationList as $property => $relation) {
			if ($relation->setter) {
				$setter = $relation->setter;
				$row->$setter($relation->evaluate($row, $parameterMap, $mapper));
			}
			else {
				$row->$property = $relation->evaluate($row, $parameterMap, $mapper);
			}
		}
	}
}
?>