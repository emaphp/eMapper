<?php
namespace eMapper\Engine\Generic;

use eMapper\Engine\Generic\Regex\GenericRegex;
use eMapper\Type\TypeManager;
use eMapper\Mapper;

/**
 * The Driver class constains all methods used to manage a database connection.
 * @author emaphp
 */
abstract class Driver {
	use \FluentConfiguration;
	
	/**
	 * Database connection
	 * @var resource | object
	 */
	protected $connection;
	
	/**
	 * Regex builder
	 * @var \eMapper\Engine\Generic\Regex\GenericRegex
	 */
	protected $regex;

	public function __construct() {
		$this->preserveInstance = true;
	}
	
	/**
	 * Obtains current connection
	 * @return resource|object
	 */
	public function getConnection() {
		return $this->connection;
	}

	/**
	 * Returns a regex builder for the current engine
	 * @return \eMapper\Engine\Generic\Regex\GenericRegex
	 */
	public function getRegex() {
		return $this->regex;
	}
		
	/**
	 * Connects to database
	 */
	public abstract function connect();
	
	/**
	 * Executes a query and returns a result
	 * @param string $query
	 */
	public abstract function query($query);
	
	/**
	 * Frees a result
	 * @param mixed $result
	 */
	public abstract function freeResult($result);
	
	/**
	 * Closes a database connection
	 */
	public abstract function close();
	
	/**
	 * Obtains last generated error message
	 */
	public abstract function getLastError();
	
	/**
	 * Obtains last generated id
	 * @return int
	 */
	public abstract function getLastId();
	
	/**
	 * Begins a transaction
	 */
	public abstract function begin();
	
	/**
	 * Commits current transaction
	 */
	public abstract function commit();
	
	/**
	 * Rollbacks current transaction
	 */
	public abstract function rollback();
	
	/**
	 * Returns a TypeManager instance for current engine
	 * @return \eMapper\Type\TypeManager
	 */
	public abstract function buildTypeManager();
	
	/**
	 * Returns a statement instance for current engine
	 * @param \eMapper\Type\TypeManager $typeManager
	 */
	public abstract function buildStatement(TypeManager $typeManager);
	
	/**
	 * Returns a result iterator for the given result
	 * @param mixed $result
	 */
	public abstract function buildResultIterator($result);
	
	/**
	 * Returns a new stored procedure instance
	 * @param \eMapper\Mapper $mapper
	 * @param string $procedure
	 * @return \eMapper\Procedure\StoredProcedure
	 */
	public abstract function buildProcedureCall(Mapper $mapper, $procedure);
	
	/**
	 * Throws a query exception
	 * @param string $query
	 */
	public abstract function throwQueryException($query);
}