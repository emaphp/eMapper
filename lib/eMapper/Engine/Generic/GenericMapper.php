<?php
namespace eMapper\Engine\Generic;

use eMapper\Engine\Generic\Configuration\GenericMapperConfiguration;
use eMapper\Statement\Aggregate\StatementNamespaceAggregate;
use eMapper\Type\TypeHandler;

abstract class GenericMapper {
	use GenericMapperConfiguration;
	use StatementNamespaceAggregate;
	
	const OBJECT_TYPE_REGEX = '@^(object|obj+)(:[A-z]{1}[\w|\\\\]*)?(\[\]|\[(\w+)\]|\[(\w+)(:[A-z]{1}[\w]*)\])?$@';
	const ARRAY_TYPE_REGEX  = '@^(array|arr+)(\[\]|\[(\w+)\]|\[(\w+)(:[A-z]{1}[\w]*)\])?$@';
	const SIMPLE_TYPE_REGEX = '@^([A-z]{1}[\w|\\\\]*)(\[\])?@';
	
	/**
	 * Applies default configuration options
	 */
	protected function applyDefaultConfig() {
		//database prefix
		$this->config['db.prefix'] = '';
		
		//dinamic sql environment class
		$this->config['dynamic.enviroment'] = 'eMapper\Environment\DynamicSQLEnvironment';
		
		//dynamic sql program class
		$this->config['dynamic.program'] = 'eMacros\Program\SimpleProgram';
		
		//use database prefix for procedure names
		$this->config['procedure.use_prefix'] = true;
		
		//default relation order
		$this->config['order.current'] = 0;
		
		//default relation order limit
		$this->config['order.limit'] = 1;
	}
	
	/**
	 * Assigns a TypeHandler instance for the given type
	 * @param string $type
	 * @param TypeHandler $typeHandler
	 * @throws \InvalidArgumentException
	 */
	public function addType($type, TypeHandler $typeHandler, $alias = null) {	
		$this->typeManager->setTypeHandler($type, $typeHandler);
	
		if (!is_null($alias)) {				
			$this->typeManager->addAlias($type, $alias);
		}
	}
	
	/*
	 * Abstract methods
	*/
	
	public abstract function query($query);
	public abstract function execute($statementId);
	public abstract function sql($query);
	public abstract function free_result($result);
	public abstract function commit();
	public abstract function rollback();
	public abstract function escape($string);
}
?>