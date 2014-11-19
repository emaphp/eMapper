<?php
namespace eMapper\Query;

use eMapper\Mapper;
use eMapper\Statement\Configuration\StatementConfiguration;

/**
 * The StoredProcedure class abstracts a database stored procedure and provides a fluent configuration interface. 
 * @author emaphp
 */
class StoredProcedure {
	use StatementConfiguration;
	
	/**
	 * Stored procedure name
	 * @var string
	 */
	protected $name;
	
	/**
	 * Database mapper
	 * @var Mapper
	 */
	protected $mapper;
	
	/**
	 * Procedure expression
	 * @var string
	 */
	protected $expression;
	
	/**
	 * Argument types
	 * @var array
	 */
	protected $types = [];
	
	/**
	 * Use database prefix
	 * @var boolean
	 */
	protected $usePrefixOption = true;
	
	/**
	 * Treat procedures as table (PostgreSQL)
	 * @var boolean
	 */
	protected $asTableOption = false;
	
	/**
	 * Escapes procedure name (PostgreSQL)
	 * @var boolean
	 */
	protected $escapeOption = false;
	
	/**
	 * Creates a new StoredProcedure instance
	 * @param Mapper $mapper
	 * @param string $name
	 */
	public function __construct(Mapper $mapper, $name) {
		$this->name = $name;
		$this->mapper = $mapper;
		$this->preserveInstance = true;
	}
	
	/**
	 * Sets argument types (variadic)
	 * @return \eMapper\Procedure\StoredProcedure
	 */
	public function types($types) {
		$this->types = is_array($types) ? $types : func_get_args();
		return $this;
	}
	
	/**
	 * Determines if the procedure is treated as a table
	 * @param boolean $as_table
	 * @return \eMapper\Procedure\StoredProcedure
	 */
	public function asTable($as_table = true) {
		$this->asTableOption = $as_table;
		return $this;
	}
	
	/**
	 * Determines if the database prefix is appended in front of the procedure name
	 * @param boolean $use_prefix
	 * @return \eMapper\Procedure\StoredProcedure
	 */
	public function usePrefix($use_prefix = true) {
		$this->usePrefixOption = $use_prefix;
		return $this;
	}
	
	/**
	 * Determines if the procedure name is escaped
	 * @param string $escape
	 * @return \eMapper\Procedure\StoredProcedure
	 */
	public function escape($escape = true) {
		$this->escapeOption = $escape;
		return $this;
	}
	
	/**
	 * Builds the SQL expression for the current procedure
	 * @param string $args
	 */
	protected function buildExpression($args) {
		//build argument expression array
		$tokens = [];
			
		if (!empty($this->types)) {
			foreach ($this->types as $type)
				$tokens[] = '%{' . $type . '}';
		}
			
		for ($i = count($tokens), $n = count($args); $i < $n; $i++)
			$tokens[] = '%{' . $i . '}';
						
		//remove additional expressions
		if (count($tokens) > count($args))
			$tokens = array_slice($tokens, 0, count($args));
				
		//build call string
		$options = [
			'prefix'     => $this->mapper->getOption('db.prefix'),
			'use_prefix' => $this->usePrefixOption,
			'escape'     => $this->escapeOption,
			'as_table'   => $this->asTableOption
		];
		
		$driver = $this->mapper->getDriver();
		$this->expression = $driver->buildCall($this->name, $tokens, $options);
	}
	
	/**
	 * Call the procedure with the submitted arguments (variadic)
	 * @return mixed
	 */
	public function call($args) {
		$args = func_get_args();
		
		if (is_null($this->expression))
			$this->buildExpression($args);
		
		array_unshift($args, $this->expression);
		return call_user_func_array([$this->mapper->merge($this->config), 'query'], $args);
	}
	
	/**
	 * Invokes the stored procedure with a list of arguments as an array
	 * @param array $args
	 * @return mixed
	 */
	public function callWith($args) {
		if (is_null($this->expression))
			$this->buildExpression($args);
		
		array_unshift($args, $this->expression);
		return call_user_func_array([$this->mapper->merge($this->config), 'query'], $args);
	}
	
	public function getName() {
		return $this->name;
	}
	
	public function getMapper() {
		return $this->mapper;
	}
	
	public function getTypes() {
		return $this->types;
	}
	
	public function getAsTable() {
		return $this->asTableOption;
	}
	
	public function getUsePrefix() {
		return $this->usePrefixOption;
	}
	
	public function getEscape() {
		return $this->escapeOption;
	}
}
?>