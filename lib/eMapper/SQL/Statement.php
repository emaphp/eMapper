<?php
namespace eMapper\SQL;

use eMapper\SQL\Configuration\StatementConfigurationContainer;
use eMapper\SQL\Aggregate\StatementAggregate;

/**
 * The Statement class represents a named query.
 * @author emaphp
 */
class Statement {
	use StatementAggregate;
	
	/**
	 * Statement ID validation regex
	 * @var string
	 */
	const STATEMENT_ID_REGEX = '@[\w]+@';
	
	/**
	 * Statement ID
	 * @var string
	 */
	public $id;
	
	/**
	 * Statement query
	 * @var string
	 */
	public $query;
	
	/**
	 * Statement options
	 * @var StatementConfigurationContainer
	 */
	public $options;
	
	/**
	 * Creates a new Statement instance
	 * @param string $id
	 * @param string $query
	 * @param StatementConfigurationContainer $options
	 * @throws \InvalidArgumentException
	 */
	public function __construct($id, $query = '', StatementConfigurationContainer $options = null) {
		//validate statement id
		$this->validateStatementId($id);
		
		if (!is_string($query)) {
			throw new \InvalidArgumentException("Query is not a valid string");
		}
				
		$this->id = $id;
		$this->query = $query;
		$this->options = $options;
	}
	
	/*
	 * CONFIGURATION BUILDER METHODS
	 */
	
	/**
	 * Generates a new statement configuration container
	 * @param array $options
	 * @return Configuration
	 */
	public static function config(array $options = null) {
		$config = new StatementConfigurationContainer();
		
		if (is_array($options)) {
			return $config->merge($options);
		}
		
		return $config;
	}
	
	/**
	 * Generates a new StatementConfigurationContainer with the specified mapping options
	 * @return StatementConfigurationContainer
	 */
	public static function type() {
		return call_user_func_array([new StatementConfigurationContainer(), 'type'], func_get_args());
	}
}
?>