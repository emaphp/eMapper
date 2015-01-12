<?php
namespace eMapper\Procedure;

use eMapper\Statement\Configuration\StatementConfiguration;
use eMapper\Mapper;

/**
 * The StoredProcedure class is an abstraction of a database stored procedure that also provides a fluent configuration interface. 
 * @author emaphp
 */
abstract class StoredProcedure {
	use StatementConfiguration;
	
	/**
	 * Procedure name
	 * @var string
	 */
	protected $name;
	
	/**
	 * Database mapper
	 * @var \eMapper\Mapper
	 */
	protected $mapper;
	
	/**
	 * Stored procedure expression
	 * @var string
	 */
	protected $expression;
	
	/**
	 * Database prefix
	 * @var string
	 */
	protected $prefix;
	
	/**
	 * Determines if database prefix is appended to procedure name
	 * @var bool
	 */
	protected $usePrefixOption;
	
	/**
	 * Argument types
	 * @var srray
	 */
	protected $argumentTypes;
	
	public function __construct(Mapper $mapper, $name) {
		$this->mapper = $mapper;
		$this->name = $name;
		$this->prefix = $mapper->getOption('db.prefix');
		$this->usePrefixOption = true;
		$this->preserveInstance = true;
	}
	
	public function getMapper() {
		return $this->mapper;
	}
	
	public function getName() {
		return $this->name;
	}
	
	/**
	 * Builds stored procedure expression
	 * @param array $args
	 */
	public abstract function build($args);
	
	/**
	 * Invokes a stored procedure with the given list of arguments
	 * @return mixed
	 */
	public function call() {
		$args = func_get_args();
		$this->build($args);
		return $this->mapper->merge($this->config)->execute($this->expression, $args);
	}
	
	/**
	 * Invokes a stored procedure with the given array of arguments
	 * @param array $args
	 * @return mixed
	 */
	public function callWith($args) {
		$this->build($args);
		return $this->mapper->merge($this->config)->execute($this->expression, $args);
	}
	
	/*
	 * CONFIGURATION
	 */
	
	/**
	 * Configures if database prefix is appended to procedure name
	 * @param bool $usePrefix
	 * @return \eMapper\Procedure\StoredProcedure
	 */
	public function usePrefix($usePrefix = true) {
		$this->usePrefixOption = $usePrefix;
		return $this;
	}
	
	/**
	 * Configures argument types
	 * @param array $types
	 * @return \eMapper\Procedure\StoredProcedure
	 */
	public function argTypes($types) {
		is_array($types) ? $this->argumentTypes = $types : $this->argumentTypes = func_get_args();
		return $this;
	}
}