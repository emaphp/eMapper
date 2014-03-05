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
			
			switch (pg_field_type($this->result, $i)) {
				case 'bit':
				case 'boolean': {
					$type = 'boolean';
				}
				break;
				
				case 'character':
				case 'char':
				case 'text':
				case 'json':
				case 'xml':
				case 'varchar': {
					$type = 'string';
				}
				break;
				
				case 'bytea':
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