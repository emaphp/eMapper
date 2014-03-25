<?php
namespace eMapper\Result\Mapper;

use eMapper\Result\ResultInterface;
use eMapper\Result\ArrayType;
use eMapper\Reflection\Profiler;

class ArrayTypeMapper extends ComplexTypeMapper {
	/**
	 * Builds a result map property list
	 * @throws \UnexpectedValueException
	 */
	protected function validateResultMap() {
		//get relations
		$this->relationList = Profiler::getClassProfile($this->resultMap)->dynamicAttributes;
	
		//obtain mapped properties
		$this->propertyList = Profiler::getClassProfile($this->resultMap)->propertiesConfig;
		
		//store type handlers while on it
		$this->typeHandlers = array();
	
		foreach ($this->propertyList as $name => $config) {
			//validate column reference
			if (!array_key_exists($config->column, $this->columnTypes)) {
				throw new \UnexpectedValueException("Column '{$config->column}' was not found on this result");
			}
				
			//obtain type handler
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
	
	/**
	 * Returns a mapped row from a fetched array
	 * @param array $row
	 * @return array
	 * @throws \UnexpectedValueException
	 */
	protected function map($row) {
		$result = array();
	
		if (is_null($this->resultMap)) {
			foreach ($row as $column => $value) {
				$typeHandler = $this->columnHandler($column);
				$result[$column] = is_null($row[$column]) ? null : $typeHandler->getValue($value);
			}
		}
		else {
			foreach ($this->propertyList as $name => $config) {
				$column = $config->column;
				$typeHandler = $this->typeHandlers[$name];
				$result[$name] = is_null($row[$column]) ? null : $typeHandler->getValue($row[$column]);
			}
		}
	
		return $result;
	}
	
	/**
	 * Returns a mapped array from a mysqli_result object
	 * @param ResultInterface $result
	 * @param int $resultType
	 */
	public function mapResult(ResultInterface $result, $resultType = ArrayType::BOTH) {
		//check numer of rows returned
		if ($result->countRows() == 0) {
			return null;
		}

		//get result column types
		$this->columnTypes = $result->columnTypes($resultType);
		
		//validate result map (if any)
		if (isset($this->resultMap)) {
			$this->validateResultMap();
		}

		//map row
		return $this->map($result->fetchArray($resultType));
	}
	
	/**
	 * Returns a list of mapped arrays from a mysqli_result object
	 * @param ResultInterface $result
	 * @param string $index
	 * @param string $indexType
	 * @param string $group
	 * @param string $groupType
	 * @param int $resultType
	 * @param \UnexpectedValueException
	 */
	public function mapList(ResultInterface $result, $index = null, $indexType = null, $group = null, $groupType = null, $resultType = ArrayType::BOTH) {
		//check numer of rows returned
		if ($result->countRows() == 0) {
			return array();
		}
	
		//get result column types
		$this->columnTypes = $result->columnTypes($resultType);
	
		//validate result map (if any)
		if (isset($this->resultMap)) {
			$this->validateResultMap();
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
					$row = $result->fetchArray($resultType);
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
						$key = $row[$groupColumn];
						
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
						$idx = $row[$indexColumn];
						
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
					$row = $result->fetchArray($resultType);
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
						$idx = $row[$indexColumn];
						
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
					$row = $result->fetchArray($resultType);
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
						$key = $row[$groupColumn];
						
						if (is_null($key)) {
							throw new \UnexpectedValueException("Null value found when grouping by column '$groupColumn'");
						}
							
						//obtain group value
						$key = $groupTypeHandler->getValue($key);
						
						if (!is_int($key) && !is_string($key)) {
							throw new \UnexpectedValueException("Obtained group key in column '$groupColumn' is neither an integer or string");
						}
					}
					
					//store value
					if (isset($list[$key])) {
						$list[$key][] = $mappedRow;
					}
					else {
						$list[$key] = [$mappedRow];
						$this->groupKeys[] = $key;
					}
					
					$result->next();
				}
			}
		}
		else {
			while ($result->valid()) {
				$list[] = $this->map($result->fetchArray($resultType));
				$result->next();
			}
		}
		
		return $list;
	}
	
	public function relate(&$row, $parameterMap, $mapper) {
		foreach ($this->relationList as $property => $relation) {
			$row[$property] = $relation->evaluate($row, $parameterMap, $mapper);
		}
	}
}
?>