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
		
		$list = array();
		
		if (isset($index) || isset($group)) {
			//validate index column
			if (isset($index)) {
				list($indexColumn, $indexTypeHandler) = $this->validateIndex($index, $indexType);
			}
			
			//validate group
			if (isset($group)) {
				list($groupColumn, $groupTypeHandler) = $this->validateGroup($group, $groupType);
			}
			
			if (isset($index) && isset($group)) {
				$this->groupKeys = array();
				
				while ($result->valid()) {
					///get row
					$row = $result->fetchObject();
					
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
						
					//store value
					if (isset($list[$key])) {
						$list[$key][$idx] = $this->map($row);
					}
					else {
						$list[$key] = [$idx => $this->map($row)];
						$this->groupKeys[] = $key;
					}
					
					$result->next();
				}
			}
			elseif (isset($index)) {
				while ($result->valid()) {
					///get row
					$row = $result->fetchObject();
					
					//get index value
					$key = $row->$indexColumn;
					
					//check if index value equals null
					if (is_null($key)) {
						throw new \UnexpectedValueException("Null value found when indexing by column '$indexColumn'");
					}
					
					//obtain index value
					$key = $indexTypeHandler->getValue($key);
						
					if (!is_int($key) && !is_string($key)) {
						throw new \UnexpectedValueException("Obtained index in column '$indexColumn' key is neither an integer or string");
					}
						
					//store value and get next one
					$list[$key] = $this->map($row);
					
					$result->next();
				}
			}
			else {
				$this->groupKeys = array();
				
				while ($result->valid()) {
					///get row
					$row = $result->fetchObject();
					
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
	
	public function relate(&$row, $mapper) {
		foreach ($this->relationList as $property => $relation) {
			if (array_key_exists('setter', $this->propertyList)) {
				$setter = $this->propertyList[$property]['setter'];
				$row->$setter($relation->evaluate($row, $mapper));
			}
			else {
				$row->$property = $relation->evaluate($row, $mapper);
			}
		}
	}
}
?>