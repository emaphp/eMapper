<?php
namespace eMapper\Result\Mapper;

use eMapper\Result\ResultInterface;
use eMapper\Reflection\Profiler;

class ArrayTypeMapper extends ComplexTypeMapper {
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
				$typeHandler = $this->typeManager->getTypeHandler($this->columnTypes[$column]);
				$result[$column] = $typeHandler->getValue($value);
			}
		}
		else {
			//get result map properties
			$fields = Profiler::getClassProperties($this->resultMap);
				
			foreach ($fields as $name => $field) {
				$column = $this->propertyList[$name]['column'];
				$typeHandler = $this->propertyList[$name]['handler'];
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
	public function mapResult(ResultInterface $result, $resultType = ResultInterface::BOTH) {
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

		//map row
		return $this->map($result->fetchArray($resultType));
	}
	
	/**
	 * Returns a list of mapped arrays from a mysqli_result object
	 * @param ResultInterface $result
	 * @param string $index
	 * @param string $type
	 * @param int $resultType
	 * @param \UnexpectedValueException
	 */
	public function mapList(ResultInterface $result, $index = null, $type = null, $resultType = MYSQLI_BOTH) {
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
			while (($row = $result->fetchArray($resultType)) !== null) {
				$list[] = $this->map($row);
			}
		}
		else {
			if (is_null($this->resultMap)) {
				if (!array_key_exists($index, $this->columnTypes)) {
					if (is_numeric($index) && array_key_exists((int) $index, $this->columnTypes)) {
						$index = (int) $index;
					}
					else {
						throw new \UnexpectedValueException("Index column '$index' not found");
					}
				}
	
				$type = is_null($type) ? $this->columnTypes[$index] : $type;
				$column = $index;
				
				//obtain index handler
				$typeHandler = $this->typeManager->getTypeHandler($type);
				
				if ($typeHandler === false) {
					throw new \UnexpectedValueException("Unknown type '$type' defined for index '$index'");
				}
			}
			else {
				if (!array_key_exists($index, $this->propertyList)) {
					throw new \UnexpectedValueException("Index property '$index' was not found in result map");
				}
				
				$column =  $this->propertyList[$index]['column'];
				$typeHandler = $this->propertyList[$index]['handler'];
			}
	
			$indexes = array();
				
			while ($result->valid()) {
				$row = $result->fetchArray($resultType);
				
				//get index value
				$key = $row[$column];
	
				//check if index value equals null
				if (is_null($key)) {
					throw new \UnexpectedValueException("Null value found when indexing by column '$index'");
				}
				else {
					//obtain index key
					$key = $typeHandler->getValue($key);
	
					if (!is_int($key) && !is_string($key)) {
						throw new \UnexpectedValueException("Obtained index key is neither an integer or string");
					}
						
					if (in_array($key, array_keys($indexes))) {
						if ($indexes[$key] === 0) {
							$value = $list[$key];
							$list[$key] = array();
							$list[$key][] = $value;
						}
	
						$list[$key][] = $this->map($row);
						$indexes[$key]++;
					}
					else {
						$list[$key] = $this->map($row);
						$indexes[$key] = 0;
					}
				}
				
				$result->next();
			}
		}
	
		return $list;
	}
}
?>