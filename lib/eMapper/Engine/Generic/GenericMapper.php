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
		
		//dynamic sql environment id
		$this->config['environment.id'] = 'default';
		
		//dynamic sql environment class
		$this->config['enviroment.class'] = 'eMapper\Dynamic\Environment\DynamicSQLEnvironment';
		
		//use database prefix for procedure names
		$this->config['procedure.use_prefix'] = true;
		
		//default relation depth
		$this->config['depth.current'] = 0;
		
		//default relation depth limit
		$this->config['depth.limit'] = 1;
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
	
	/**
	 * Configures dynamic SQL environment
	 * @param string $id Environment id
	 * @param string $class Environment class
	 * @param string $import Packages to import
	 * @param string $program Program class
	 * @throws \InvalidArgumentException
	 */
	public function configureEnvironment($id, $class = 'eMapper\Dynamic\Environment\DynamicSQLEnvironment', $import = null, $program = 'eMacros\Program\SimpleProgram') {
		if (empty($import)) {
			$import = null;
		}
		elseif (is_string($import)) {
			$import = array($import);
		}
		
		//validate program class
		if (!class_exists($program)) {
			throw new \InvalidArgumentException("Program class '$program' not found");
		}
		
		$rc = new \ReflectionClass($program);
		
		if (!$rc->isSubclassOf('eMacros\Program\Program')) {
			throw new \InvalidArgumentException("Class '$program' is not a valid program class");
		}
		
		//apply values
		$this->config['environment.id'] = $id;
		$this->config['environment.class'] = $class;
		$this->config['environment.import'] = $import;
		$this->config['environment.programs'] = $program;
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