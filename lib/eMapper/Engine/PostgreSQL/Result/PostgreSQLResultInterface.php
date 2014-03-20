<?php
namespace eMapper\Engine\PostgreSQL\Result;

use eMapper\Result\ResultInterface;

class PostgreSQLResultInterface extends ResultInterface {
	public $resultTypes = array(self::BOTH => PGSQL_BOTH, self::ASSOC => PGSQL_ASSOC, self::NUM => PGSQL_NUM);
	
	public function countRows() {
		return pg_num_rows($this->result);
	}
	
	public function columnTypes($resultType = self::ASSOC) {
		$num_fields = pg_num_fields($this->result);
		$types = array();
		
		for ($i = 0; $i < $num_fields; $i++) {
			$name = pg_field_name($this->result, $i);
			$type = pg_field_type($this->result, $i);
			
			switch ($type) {
				case 'bit':
				case 'boolean': {
					$type = 'boolean';
				}
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
				case 'numeric': {
					$type = 'integer';
				}
				break;
				
				case 'float4':
				case 'float8':
				case 'real':
				case 'double precision': {
					$type = 'float';
				}
				break;
				
				case 'time': {
					$type = 'string';
				}
				break;
				
				case 'date': {
					$type = 'date';
				}
				break;
				
				case 'timestamp': {
					$type = 'DateTime';
				}
				
				case 'bytea': {
					$type = 'blob';
				}
				
				case 'character':
				case 'char':
				case 'text':
				case 'json':
				case 'xml':
				case 'varchar':
				default: {
					$type = 'string';
				}
				break;
				break;
			}
			
			//store type
			if ($resultType & self::NUM) {
				$types[$i] = $type;
			}
			
			if ($resultType & self::ASSOC) {
				$types[$name] = $type;
			}
		}
		
		return $types;
	}
	
	public function fetchArray($resultType = self::BOTH) {
		return pg_fetch_array($this->result, null, $this->resultTypes[$resultType]);
	}
	
	public function fetchObject($className = null) {
		return pg_fetch_object($this->result, null, $classname);
	}
}
?>