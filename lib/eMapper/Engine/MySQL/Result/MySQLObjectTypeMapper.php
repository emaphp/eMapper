<?php
namespace eMapper\MySQL\Result;

use eMapper\Type\TypeManager;
use eMapper\Engine\MySQL\Exception\MySQLMapperException;
use eMapper\Result\ObjectTypeMapper;
use eMapper\Type\TypeHandler;

class MySQLObjectTypeMapper extends MySQLComplexTypeMapper implements ObjectTypeMapper {
	/**
	 * Default conversion class
	 * @var string
	 */
	protected $defaultClass;
	
	/**
	 * ObjectMapper constructor
	 * @param MySQLTypeManager $typeManager
	 * @param Model $model
	 * @param string $defaultClass
	 */
	public function __construct(TypeManager $typeManager, $model = null, $defaultClass = 'stdClass') {
		parent::__construct($typeManager, $model);
		$this->defaultClass = $defaultClass;
	}
	
	/**
	 * Returns a mapped row from a fetched object
	 * @param object $row
     * @param Model $model
	 * @return object
	 * @throws MySQLMapperException
	 */
	protected function map($row, $model) {
		$obj = $model->__instance($this->defaultClass);
		
		foreach ($model->__fields as $field => $properties) {
			//get column name
			$column = $properties['column'];
			
			//get setter
			$setter = array_key_exists('setter', $properties) ? $properties['setter'] : null;
			
            //check if column is present
            if (!property_exists($row, $column)) {
            	if ($model->__strict) {
                	throw new MySQLMapperException(sprintf("Unknown column '%s' referenced by property '%s' in class %s", $column, $field, get_class($this)));
            	}
            	
            	continue;
            }
            
			if (is_null($row->$column)) {
				$val = null;
			}
			else {
				$typeHandler = array_key_exists('type', $properties) && !is_null($properties['type']) ? $this->typeManager->getTypeHandler($properties['type']) : $this->column_handler($column);
					
				//check if type handler exists
				if ($typeHandler === false) {
					throw new MySQLMapperException("No type handler defined for type '{$properties['type']}'");
				}
					
				$val = $typeHandler->getValue($row->$column);
			}
			
			//set value
			if (!is_null($setter)) {
				//get ifd setter is public
				$rm = new \ReflectionMethod($this->defaultClass, $setter);
					
				if (!$rm->isPublic()) {
					throw new MySQLMapperException(sprintf("Setter method '%s' is not public in class '%s'", $setter, $this->defaultClass));
				}
					
				$obj->$setter($val);
			}
			elseif ($this->defaultClass != 'stdClass') {
				//check property existence
				if (property_exists($this->defaultClass, $field)) {
					$rp = new \ReflectionProperty($this->defaultClass, $field);
			
					if (!$rp->isPublic()) {
						throw new MySQLMapperException(sprintf("Property '%s' is not public on class '%s'", $field, $this->defaultClass));
					}
				}
					
				$obj->$field = $val;
			}
			else {
				$obj->$field = $val;
			}
		}
		
		return $obj;
	}
	
	/**
	 * Returns a mapped object from a mysqli_result object
	 * @param \mysqli_result $result
	 * @throws MySQLMapperException
	 * @return NULL|object
	 */
	public function mapResult($result) {
		//check if result is a valid mysqli_result
		if (!($result instanceof \mysqli_result)) {
			throw new MySQLMapperException("Result is not a valid mysqli_result object");
		}
		
		//check numer of rows returned
		if ($result->num_rows == 0) {
			return null;
		}
		
		//get result column types
		$this->columnTypes = $this->column_types($result);
		
		//get row as an object and map using its model
		return $this->map($result->fetch_object(), $this->build_model());
	}
	
	/**
	 * Returns a list of objects from a mysqli_result object
	 * @param \mysqli_result $result
	 * @param string $index
	 * @param string $type
	 * @throws MySQLMapperException
	 * @return NULL|array
	 */
	public function mapList($result, $index = null, $type = null) {
		//check numer of rows returned
		if ($result->num_rows == 0) {
			return array();
		}
		
		//get result column types
		$this->columnTypes = $this->column_types($result);
		
		//generate model
		$model = $this->build_model();
		
		$list = array();
			
		//check if an index has been defined
		if (is_null($index)) {
			while (($row = $result->fetch_object()) !== null) {
				$list[] = $this->map($row, $model);
			}
		}
		else {
			//check index
			if (!array_key_exists($index, $model->__fields)) {
				throw new MySQLMapperException("Index '$index' not found");
			}
			
			//obtain index handler
			$type = is_null($type) ? $model->__fields[$index]['type'] : $type;
			$typeHandler = $this->typeManager->getTypeHandler($type);
			
			if ($typeHandler === false) {
				throw new MySQLMapperException("Unknown type '$type' defined for index '$index'");
			}
			
			$column = $model->__fields[$index]['column'];
			$indexes = array();
			
			while (($row = $result->fetch_object()) !== null) {
				//get index value
				$key = $row->$column;
		
				//check if index value equals null
				if (is_null($key)) {
					throw new MySQLMapperException("Null value found when indexing by column '$index'");
				}
				else {
					//obtain index key
					$key = $typeHandler->getValue($key);
					
					if (in_array($key, array_keys($indexes))) {
						if ($indexes[$key] === 0) {
							$value = $list[$key];
							$list[$key] = array();
							$list[$key][] = $value;
						}
						
						$list[$key][] = $this->map($row, $model);
						$indexes[$key]++;
					}
					else {
						$list[$key] = $this->map($row, $model);
						$indexes[$key] = 0;
					}
				}
			}
		}
		
		return $list;
	}
}
?>