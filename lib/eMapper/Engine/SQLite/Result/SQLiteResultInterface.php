<?php
namespace eMapper\Engine\SQLite\Result;

use eMapper\Result\ResultInterface;

class SQLiteResultInterface extends ResultInterface {
	public $resultTypes = array(self::BOTH => SQLITE3_BOTH, self::ASSOC => SQLITE3_ASSOC, self::NUM => SQLITE3_NUM);
	
	/* (non-PHPdoc)
	 * @see \eMapper\Result\ResultInterface::countRows()
	 */
	public function countRows() {
		// TODO: Auto-generated method stub
		return $this->result->numColumns();
	}

	/* (non-PHPdoc)
	 * @see \eMapper\Result\ResultInterface::columnTypes()
	 */
	public function columnTypes() {
		// TODO: Auto-generated method stub
		$num_columns = $this->result->numColumns();
		$types = array();
		
		for ($i = 0; $i < $num_columns; $i++) {
			$name = $this->result->columnName($i);
			
			switch ($this->result->columnType($i)) {
				case SQLITE3_INTEGER:
					$type = 'integer';
					break;
					
				case SQLITE3_FLOAT:
					$type = 'float';
					break;
					
				case SQLITE3_BLOB:
					$type = 'blob';
					break;
					
				case SQLITE3_NULL:
					$type = 'null';
					break;
					
				case SQLITE3_TEXT:
				default:
					$type = 'string';
					break;
			}
			
			$types[$i] = $type[$name] = $type;
		}
		
		return $types;
	}

	public function fetchArray($resultType = self::BOTH) {
		return $this->result->fetchArray($this->resultTypes[$resultType]);
	}
	
	/* (non-PHPdoc)
	 * @see \eMapper\Result\ResultInterface::fetchObject()
	 */
	public function fetchObject($className = null) {
		// TODO: Auto-generated method stub
		return (object) $this->result->fetchArray(SQLITE3_ASSOC);
	}

}
?>