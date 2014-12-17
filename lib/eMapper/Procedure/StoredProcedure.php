<?php
namespace eMapper\Procedure;

use eMapper\Statement\Configuration\StatementConfiguration;
use eMapper\Mapper;

/**
 * The StoredProcedure class is an abstraction of a database stored procedure that also provides a fluent configuration interface. 
 * @author emaphp
 */
class StoredProcedure {
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
	
	public function __construct(Mapper $mapper, $name) {
		$this->mapper = $mapper;
		$this->name = $name;
		$this->preserveInstance = true;
		
		//apply default config
		$this->config = [
			'proc.returnSet'  => true,
			'proc.escapeName' => false,
			'proc.usePrefix'  => true,
			'proc.prefix'     => $mapper->getOption('db.prefix')
		];
	}
	
	public function getMapper() {
		return $this->mapper;
	}
	
	public function getName() {
		return $this->name;
	}
	
	public function build($args) {
		if (isset($this->expression))
			return;
		
		$tokens = [];
		$types = $this->getOption('proc.types');
			
		if (!empty($types)) {
			foreach ($types as $type)
				$tokens[] = '%{' . $type . '}';
		}
		
		for ($i = count($tokens), $n = count($args); $i < $n; $i++)
			$tokens[] = '%{' . $i . '}';
						
		//remove additional expressions
		if (count($tokens) > count($args))
			$tokens = array_slice($tokens, 0, count($args));
		
		$this->expression = $this->mapper->getDriver()->buildCall($this->name, $tokens, $this->config);
	}
	
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
		array_unshift($args, $this->expression);
		return $this->mapper->merge($this->config)->execute($this->expression, $args);
	}
	
	/*
	 * CONFIGURATION
	 */
	
	public function returnSet($returnSet = true) {
		return $this->option('proc.returnSet', $returnSet);
	}
	
	public function escapeName($escapeName = true) {
		return $this->option('proc.escapeName', $escapeName);
	}
	
	public function usePrefix($usePrefix = true) {
		return $this->option('proc.usePrefix', $usePrefix);
	}
	
	public function types($types) {
		return is_array($types) ? $this->option('proc.types', $types) : $this->option('proc.types', func_get_args());
	}
}