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
	 * ObjectMapper constructor
	 * @param TypeManager $typeManager
	 * @param string $resultMap
	 * @param string $defaultClass
	 */
	public function __construct(TypeManager $typeManager, $resultMap = null, $parameterMap = null, $defaultClass = 'stdClass') {
		parent::__construct($typeManager, $resultMap, $parameterMap);
		$this->defaultClass = $defaultClass;
	}
	
	protected function map($row) {
		if (isset($this->resultMap)) {
			if (Profiler::isEntity($this->resultMap)) {
				$reflectionClass = Profiler::getReflectionClass($this->resultMap);
				$instance = $reflectionClass->newInstance();
			}
			else {
				$profile = Profiler::getClassAnnotations($this->resultMap);
				$class = $profile->has('defaultClass') ? $profile->get('defaultClass') : 'stdClass';
				$instance = new $class;
			}
			
			$fields = Profiler::getClassProperties($this->resultMap);
			
			foreach ($this->propertyList as $name => $props) {
				$column = $props['column'];
				$typeHandler = $props['handler'];
				
				if ($instance instanceof \stdClass) {
					$instance->$name = $typeHandler->getValue($row->$column);
				}
				elseif ($instance instanceof \ArrayObject) {
					$instance['name'] = $typeHandler->getValue($row->$column);
				}
				else {
					if (array_key_exists('setter', $props)) {
						$setter = $props['setter'];
						$instance->$setter($typeHandler->getValue($row->$column));
					}
					else {
						$instance->$name = $typeHandler->getValue($row->$column);
					}
				}
			}
		}
		else {
			$instance = new $this->defaultClass;
			
			if ($this->defaultClass == 'stdClass') {
				foreach ($this->columnTypes as $column => $type) {
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
				$reflectionClass = Profiler::getReflectionClass($this->defaultClass);
				
				foreach ($this->columnTypes as $column => $type) {
					if ($reflectionClass->hasProperty($column)) {
						//validate property
						$property = $reflectionClass->getProperty($column);
						
						if (!$property->isPublic()) {
							throw new \UnexpectedValueException(sprintf("Property %s in class %s has not public access", $property->getName(), $this->defaultClass));
						}
						
						//set values
						$typeHandler = $this->columnHandler($column);
						$instance->$column = $typeHandler->getValue($row->$column);
					}
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
		
		//get row as an object and map using its model
		return $this->map($result->fetchObject());
	}
	
	/**
	 * Returns a list of objects from a mysqli_result object
	 * @param ResultInterface $result
	 * @param string $index
	 * @param string $type
	 * @throws MySQLMapperException
	 * @return NULL|array
	 */
	public function mapList(ResultInterface $result, $index = null, $type = null) {
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
		
		$list = array();
			
		//check if an index has been defined
		if (is_null($index)) {
			while ($result->valid()) {
				$list[] = $this->map($result->fetchObject());
				$result->next();
			}
		}
		else {
			$group = (bool) preg_match('/^!/', $index);
			$index = $group ? substr($index, 1) : $index;
			
			if (is_null($this->resultMap)) {
				if (!array_key_exists($index, $this->columnTypes)) {
					throw new \InvalidArgumentException("Index '$index' not found");
				}
				
				$column = $index;
				
				if (is_null($type)) {
					$typeHandler = $this->typeManager->getTypeHandler($this->columnTypes[$index]);
				}
				else {
					$typeHandler = $this->typeManager->getTypeHandler($type);
					
					if ($typeHandler === false) {
						throw new \InvalidArgumentException("No type handler found for type '$type'");
					}
				}
			}
			else {				
				if (!array_key_exists($index, $this->propertyList)) {
					throw new \UnexpectedValueException("Property '$index' not found");
				}
				
				$column = $this->propertyList[$index]['column'];
				
				if (is_null($type)) {
					$typeHandler = $this->propertyList[$index]['handler'];
				}
				else {
					$typeHandler = $this->typeManager->getTypeHandler($type);
						
					if ($typeHandler === false) {
						throw new \InvalidArgumentException("No type handler found for type '$type'");
					}
				}
			}
	
			$this->groupKeys = array();
				
			while ($result->valid()) {
				///get row
				$row = $result->fetchObject();

				//get index value
				$key = $row->$column;
	
				//check if index value equals null
				if (is_null($key)) {
					throw new \UnexpectedValueException("Null value found when indexing by column '$index'");
				}
				
				//obtain index key
				$key = $typeHandler->getValue($key);
					
				if ($group) {
					if (isset($list[$key])) {
						$list[$key][] = $this->map($row);
					}
					else {
						$list[$key] = array($this->map($row));
						$this->groupKeys[] = $key;
					}
				}
				else {
					$list[$key] = $this->map($row);
				}
				
				$result->next();
			}
		}
	
		return $list;
	}
	
	public function relate(&$row, $mapper) {
		foreach ($this->relationList as $property => $relation) {
			if (array_key_exists('setter', $this->propertyList)) {
				$setter = $this->propertyList[$property]['setter'];
				$row->$setter($relation->evaluate($row, $mapper));
			}
			else {
				$row->$property($relation->evaluate($row, $mapper));
			}
		}
	}
}
?>