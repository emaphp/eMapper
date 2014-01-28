<?php
namespace eMapper\Engine\MySQL\Configuration;

trait MySQLMapperConfiguration {
	
	/**
	 * Determines if the specified argument types for a stored procedure are valid
	 * @param array $procedure_types
	 * @throws MySQLMapperException
	 */
	protected function check_procedure_types($procedure_types) {
		$types = $this->typeManager->getTypesList();
	
		foreach ($procedure_types as $ptype) {
			if (!is_string($ptype) || empty($ptype)) {
				throw new \InvalidArgumentException("Parameter types must be specified as valid strings");
			}
	
			if (!in_array($ptype, $types)) {
				//check if type has an assigned handler (check by classname with and without namespaces)
				$typeHandler = $this->typeManager->getTypeHandler($ptype);
	
				if ($typeHandler === false) {
					throw new \InvalidArgumentException("Unknown type '$ptype' specified");
				}
			}
		}
	}
	
	/**
	 * Sets parameter types for a store procedure call
	 * @return MySQLMapperConfiguration
	 */
	public function sp_types() {
		$args = func_get_args();
		$this->check_procedure_types($args);
		return $this->merge(array('procedure.types' => $args));
	}
	
	/**
	 * Determines if the database prefix must be appended in front of the procedure name
	 * @param boolean $use_prefix
	 */
	public function sp_prefix($use_prefix) {
		return $this->merge(array('procedure.use_prefix' => (boolean) $use_prefix));
	}
	
	public function safe_copy() {
		return $this->discard('map.type', 'map.params', 'map.result', 'map.parameter',
				'callback.query', 'callback.no_rows', 'callback.each', 'callback.filter',
				'procedure.types',
				'cache.provider', 'cache.key', 'cache.ttl');
	}
}