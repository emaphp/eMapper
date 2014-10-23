<?php
namespace eMapper\Type;

use eMapper\Type\Handler\SafeStringTypeHandler;
use eMapper\Type\Handler\StringTypeHandler;
use eMapper\Type\Handler\BooleanTypeHandler;
use eMapper\Type\Handler\IntegerTypeHandler;
use eMapper\Type\Handler\FloatTypeHandler;
use eMapper\Type\Handler\BlobTypeHandler;
use eMapper\Type\Handler\DatetimeTypeHandler;
use eMapper\Type\Handler\NullTypeHandler;
use eMapper\Type\Handler\DateTypeHandler;
use eMapper\Type\Handler\JSONTypeHandler;

/**
 * A TypeManager class manages all types and aliases defined for a database mapper.
 * @author emaphp
 */
class TypeManager {
	/**
	 * Type handlers list
	 * @var array
	 */
	protected $typeHandlers;
	
	/**
	 * Aliases list
	 * @var array
	 */
	protected $aliases;
	
	public function __construct() {
		$this->typeHandlers = ['string'   => new StringTypeHandler(),
							   'boolean'  => new BooleanTypeHandler(),
							   'integer'  => new IntegerTypeHandler(),
							   'float'    => new FloatTypeHandler(),
							   'blob'     => new BlobTypeHandler(),
							   'DateTime' => new DatetimeTypeHandler(),
							   'date'     => new DateTypeHandler(),
							   'sstring'  => new SafeStringTypeHandler(),
							   'json'     => new JSONTypeHandler(),
							   'null'     => new NullTypeHandler()];
		
		$this->aliases = ['ss' => 'sstring', 'sstr' => 'sstring',
						  's' => 'string', 'str' => 'string',
						  'b' => 'boolean', 'bool' => 'boolean',
						  'i' => 'integer', 'int' => 'integer',
						  'double' => 'float', 'real' => 'float', 'f' => 'float',
						  'x' => 'blob', 'bin' => 'blob',
						  'dt' => 'DateTime', 'timestamp' => 'DateTime', 'datetime' => 'DateTime',
						  'd' => 'date'];
	}
	
	/**
	 * Obtains the list of typehandlers defined
	 * @return array
	 */
	public function getTypeHandlers() {
		return $this->typeHandlers;
	}
	
	/**
	 * Obtains the aliases list defined
	 * @return array
	 */
	public function getAliases() {
		return $this->aliases;
	}
	
	/**
	 * Associates a type handler to a given type
	 * @param string $type
	 * @param TypeHandler $typeHandler
	 * @throws \InvalidArgumentException
	 */
	public function setTypeHandler($type, TypeHandler $typeHandler) {
		if (!is_string($type) || empty($type))
			throw new \InvalidArgumentException("Type must be defined as a string");	
		$this->typeHandlers[$type] = $typeHandler;
	}
	
	/**
	 * Stores a new alias for a given type
	 * @param string $type
	 * @param string $alias
	 * @throws \InvalidArgumentException
	 */
	public function addAlias($type, $alias) {
		if (!is_string($type) || empty($type))
			throw new \InvalidArgumentException("Type must be defined as a string");
		
		if (!is_string($alias) || empty($alias))
			throw new \InvalidArgumentException("Alias must be defined as a string");
		
		$this->aliases[$alias] = $type;
	}
	
	/**
	 * Obtains the type handler assigned to a type
	 * @param string $type_or_alias
	 * @return boolean|TypeHandler
	 */
	public function getTypeHandler($type_or_alias) {		
		//verify if its an alias
		if (array_key_exists($type_or_alias, $this->aliases))
			$type_or_alias = $this->aliases[$type_or_alias];
		
		//check type existence
		if (!array_key_exists($type_or_alias, $this->typeHandlers))
			return false;
		
		return $this->typeHandlers[$type_or_alias];
	}
	
	/**
	 * Returns entire type/alias list
	 * @return array
	 */
	public function getTypesList() {
		return array_merge(array_keys($this->typeHandlers), array_keys($this->aliases));
	}
}