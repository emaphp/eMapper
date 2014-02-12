<?php
namespace eMapper\SQL;

use eMapper\SQL\Configuration\StatementConfigurationContainer;
use eMapper\SQL\Aggregate\StatementAggregate;

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
	 * @var array
	 */
	public $options;
	
	/**
	 * Create a new eStatement instance
	 * @param string $id
	 * @param string $query
	 * @param string $default_mapping
	 * @param eMappingConfiguration $options
	 * @throws \RuntimeException
	 */
	public function __construct($id, $query = '', StatementConfigurationContainer $options = null) {
		//validate statement id
		$this->validateStatementId($id);
		
		if (!is_string($query)) {
			throw new \InvalidArgumentException("Query is not a valid string");
		}
		
		if (!is_null($options) && !($options instanceof StatementConfigurationContainer)) {
			throw new \InvalidArgumentException("Statement configuration is not a valid StatementConfigurationContainer instance");
		}
		
		$this->id = $id;
		$this->query = $query;
		$this->options = $options;
	}
	
	/**
	 * CONFIGURATION BUILDER METHODS
	 */
	
	/**
	 * Generates a new statement configuration container
	 * @param array $options
	 * @return Configuration
	 */
	public static function config($options = null) {
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
		return call_user_func_array(array(new StatementConfigurationContainer(), 'type'), func_get_args());
	}
}
?>