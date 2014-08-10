<?php
namespace eMapper\Result\Mapper;

use eMapper\Type\TypeManager;
use eMapper\Result\ResultIterator;
use eMapper\Reflection\Profiler;
use eMapper\Reflection\Profile\ClassProfile;

/**
 * The ObjectMapper class maps database results to object type values.
 * @author emaphp
 */
class ObjectMapper extends ComplexMapper {
	/**
	 * Class name
	 * @var string
	 */
	protected $defaultClass;
	
	/**
	 * ObjectMapper constructor
	 * @param TypeManager $typeManager
	 * @param string $defaultClass
	 */
	public function __construct(TypeManager $typeManager, $defaultClass) {
		parent::__construct($typeManager, null);
		$this->defaultClass = $defaultClass;
		$this->properties = Profiler::getClassProfile($defaultClass)->getProperties();
	}
	
	protected function map($row) {
		$instance = new $this->defaultClass;

		foreach ($this->availableColumns as $property => $column) {
			//get value
			$typeHandler = $this->typeHandlers[$property];
			$value = is_null($row->$column) ? null : $typeHandler->getValue($row->$column);
			
			//set value
			$reflectionProperty = $this->properties[$property]->getReflectionProperty();
			$reflectionProperty->setValue($instance, $value);
		}
		
		return $instance;
	}
	
	/**
	 * Returns a mapped object from a mysqli_result object
	 * @param ResultIterator $result
	 * @return NULL|object
	 */
	public function mapResult(ResultIterator $result) {
		//check numer of rows returned
		if ($result->countRows() == 0) {
			return null;
		}
	
		//get result column types
		$this->columnTypes = $result->getColumnTypes();
		
		//build type handler list
		if ($this->defaultClass != 'stdClass' || !is_null($this->resultMap)) {
			$this->buildTypeHandlerList();
		}
		
		//get row as an object and map using its model
		return $this->map($result->fetchObject());
	}
	
	/**
	 * Returns a list of objects from a mysqli_result object
	 * @param ResultIterator $result
	 * @param string $index
	 * @param string $indexType
	 * @param string $group
	 * @param string $groupType
	 * @throws MySQLMapperException
	 * @return NULL|array
	 */
	public function mapList(ResultIterator $result, $index = null, $indexType = null, $group = null, $groupType = null) {
		//check numer of rows returned
		if ($result->countRows() == 0) {
			return [];
		}
	
		//get result column types
		$this->columnTypes = $result->getColumnTypes();
	
		//build type handler list
		if ($this->defaultClass != 'stdClass' || !is_null($this->resultMap)) {
			$this->buildTypeHandlerList();
		}
		
		$list = [];
		
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
				$this->groupKeys = [];
				
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
	
	public function evaluateFirstOrderAttributes(&$row, $mapper) {
		foreach ($this->resultMap->getFirstOrderAttributes() as $name => $attribute) {
			$reflectionProperty = $attribute->getReflectionProperty();
			$reflectionProperty->setValue($row, $attribute->evaluate($row, $mapper));
		}
	}
	
	public function evaluateSecondOrderAttributes(&$row, $mapper) {
		foreach ($this->resultMap->getSecondOrderAttributes() as $name => $attribute) {
			$reflectionProperty = $attribute->getReflectionProperty();
			$reflectionProperty->setValue($row, $attribute->evaluate($row, $mapper));
		}
	}
}
?>