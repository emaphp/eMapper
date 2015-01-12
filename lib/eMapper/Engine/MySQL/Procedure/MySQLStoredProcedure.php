<?php
namespace eMapper\Engine\MySQL\Procedure;

use eMapper\Procedure\StoredProcedure;

/**
 * The MySQLStoredProcedure class is an abstraction of a MySQL database stored procedure that also provides a fluent configuration interface.
 * @author emaphp
 */
class MySQLStoredProcedure extends StoredProcedure {
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
		$this->expression = "CALL $procedure(" . implode(',', $tokens) . ')';
	}
}