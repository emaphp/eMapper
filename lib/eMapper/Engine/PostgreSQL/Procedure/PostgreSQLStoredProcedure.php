<?php
namespace eMapper\Engine\PostgreSQL\Procedure;

use eMapper\Procedure\StoredProcedure;

/**
 * The PostgreSQLStoredProcedure class is an abstraction of a PostgreSQL database stored procedure that also provides a fluent configuration interface.
 * @author emaphp
 */
class PostgreSQLStoredProcedure extends StoredProcedure {
	/**
	 * Determines if the procedure call returns a column set
	 * @var boolean
	 */
	protected $returnSetOption = true;
	
	/**
	 * Determines if procedure name should be escaped
	 * @var boolean
	 */
	protected $escapeNameOption = false;
	
	/**
	 * Configures if the procedure call returns a column set
	 * @param boolean $returnSet
	 * @return \eMapper\Engine\PostgreSQL\Procedure\PostgreSQLStoredProcedure
	 */
	public function returnSet($returnSet = true) {
		$this->returnSetOption = $returnSet;
		return $this;
	}
	
	/**
	 * Configures if procedure name should be escaped
	 * @param boolean $escapeName
	 * @return \eMapper\Engine\PostgreSQL\Procedure\PostgreSQLStoredProcedure
	 */
	public function escapeName($escapeName = true) {
		$this->escapeNameOption = $escapeName;
		return $this;
	}
	
	public function build($args) {
		if (isset($this->expression))
			return;
	
		$tokens = [];
		if (!empty($this->argumentTypes)) {
			foreach ($this->argumentTypes as $type)
				$tokens[] = '%{' . $type . '}';
		}
	
		for ($i = count($tokens), $n = count($args); $i < $n; $i++)
			$tokens[] = '%{' . $i . '}';
	
		//remove additional expressions
		if (count($tokens) > count($args))
			$tokens = array_slice($tokens, 0, count($args));

		$procedure = $this->usePrefixOption ? $this->prefix . $this->name : $this->name;
		
		if ($this->escapeNameOption)
			$procedure = "\"$procedure\"";
		
		if ($this->returnSetOption)
			$this->expression = "SELECT * FROM $procedure(" . implode(',', $tokens) . ')';
		else
			$this->expression = "SELECT $procedure(" . implode(',', $tokens) . ')';
	}
}