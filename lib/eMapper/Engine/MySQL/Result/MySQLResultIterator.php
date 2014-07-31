<?php
namespace eMapper\Engine\MySQL\Result;

use eMapper\Result\ResultIterator;
use eMapper\Result\ArrayType;

/**
 * The MySQLResultIterator class is a MySQL database result iterator.
 * @author emaphp
 */
class MySQLResultIterator extends ResultIterator {
	public $resultTypes = array(ArrayType::BOTH => MYSQLI_BOTH, ArrayType::ASSOC => MYSQLI_ASSOC, ArrayType::NUM => MYSQLI_NUM);
	
	public function columnTypes($resultType = ArrayType::ASSOC) {
		//get result fields
		$fields = $this->result->fetch_fields();
		$types = array();
		
		for ($i = 0, $n = count($fields); $i < $n; $i++) {
			$field = $fields[$i];
		
			switch ($field->type) {
				//boolean types
				case 16://BIT
					$type = 'boolean';
					break;
		
				//numeric types
				case 1://TINYINT
				case 2://SMALLINT
				case 3://INTEGER
				case 8://BIGINT, SERIAL
				case 9://MEDIUMINT
				case 13://YEAR
					$type = 'integer';
					break;
		
				//decimal types
				case 4://FLOAT
				case 5://DOUBLE
				case 246://DECIMAL
					$type = 'float';
					break;
		
				//date types
				case 10://DATE
					$type = 'date';
					break;
		
				case 7://TIMESTAMP
				case 12://DATETIME
					$type = 'DateTime';
					break;
		
				case 11://TIME
					$type = 'string';
					break;
		
				//string types
				case 252://BLOB, TEXT
				case 253://VARCHAR
				case 254://CHAR
					$type = 'string';
					break;
		
				default:
					$type = 'string';
					break;
			}
		
			//store type
			if ($resultType & ArrayType::NUM) {
				$types[$i] = $type;
			}
			
			if ($resultType & ArrayType::ASSOC) {
				$types[$field->name] = $type;
			}
		}
		
		return $types;
	}
	
	public function countRows() {
		return $this->result->num_rows;
	}
	
	public function fetchArray($resultType = ArrayType::BOTH) {
		return $this->result->fetch_array($this->resultTypes[$resultType]);
	}
	
	public function fetchObject() {
		return $this->result->fetch_object();
	}
}
?>