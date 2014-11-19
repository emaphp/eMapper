<?php
namespace eMapper\Engine\PostgreSQL\Result;

use eMapper\Engine\Generic\Result\ResultIterator;
use eMapper\Result\ArrayType;

/**
 * The PostgreSQLResultIterator class provides an iterator for PostgreSQL
 * database results.
 * @author emaphp
 */
class PostgreSQLResultIterator extends ResultIterator {
	/**
	 * Array result types
	 * @var array
	 */
	private $resultTypes = [ArrayType::BOTH => PGSQL_BOTH, ArrayType::ASSOC => PGSQL_ASSOC, ArrayType::NUM => PGSQL_NUM];
	
	public function countRows() {
		return pg_num_rows($this->result);
	}
	
	public function getColumnTypes($resultType = ArrayType::ASSOC) {
		$num_fields = pg_num_fields($this->result);
		$types = [];
		
		for ($i = 0; $i < $num_fields; $i++) {
			$name = pg_field_name($this->result, $i);
			$type = pg_field_type($this->result, $i);

			switch ($type) {
				case 'bit':
				case 'bool':
				case 'boolean':
					$type = 'boolean';
				break;
				
				case 'int2':
				case 'int4':
				case 'int8':
				case 'smallint':
				case 'smallserial':
				case 'integer':
				case 'serial':
				case 'int4range':
				case 'bigint':
				case 'bigserial':
				case 'int8range':
				case 'decimal':
				case 'numeric':
					$type = 'integer';
				break;
				
				case 'float4':
				case 'float8':
				case 'real':
				case 'double precision':
					$type = 'float';
				break;
				
				case 'time':
					$type = 'string';
				break;
				
				case 'date':
					$type = 'DateTime';
				break;
				
				case 'timestamp':
					$type = 'DateTime';
				break;
				
				case 'bytea':
					$type = 'blob';
				break;
				
				case 'character':
				case 'char':
				case 'text':
				case 'json':
				case 'xml':
				case 'varchar':
				default:
					$type = 'string';
			}
			
			//store type
			if ($resultType & ArrayType::NUM)
				$types[$i] = $type;
			
			if ($resultType & ArrayType::ASSOC)
				$types[$name] = $type;
		}
		
		return $types;
	}
	
	public function fetchArray($resultType = ArrayType::BOTH) {
		return pg_fetch_array($this->result, null, $this->resultTypes[$resultType]);
	}
	
	public function fetchObject($className = null) {
		return pg_fetch_object($this->result);
	}
	
	public function free() {
		pg_free_result($this->result);
	}
}
?>