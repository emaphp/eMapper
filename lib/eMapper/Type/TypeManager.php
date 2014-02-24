<?php
namespace eMapper\Type;

use eMapper\Type\Handler\UnquotedStringTypeHandler;
use eMapper\Type\Handler\StringTypeHandler;
use eMapper\Type\Handler\BooleanTypeHandler;
use eMapper\Type\Handler\IntegerTypeHandler;
use eMapper\Type\Handler\FloatTypeHandler;
use eMapper\Type\Handler\BlobTypeHandler;
use eMapper\Type\Handler\DatetimeTypeHandler;
use eMapper\Type\Handler\NullTypeHandler;
use eMapper\Type\Handler\DateTypeHandler;
use eMapper\Type\Handler\JSONTypeHandler;

class TypeManager {
	/**
	 * Type handlers list
	 * @var array
	 */
	public $typeHandlers;
	
	/**
	 * Aliases list
	 * @var array
	 */
	public $aliases;
	
	public function __construct() {
		$this->typeHandlers = array('string' => new StringTypeHandler(),
				'boolean' => new BooleanTypeHandler(),
				'integer' => new IntegerTypeHandler(),
				'float' => new FloatTypeHandler(),
				'blob' => new BlobTypeHandler(),
				'DateTime' => new DatetimeTypeHandler(),
				'date' => new DateTypeHandler(),
				'ustring' => new UnquotedStringTypeHandler(),
				'json' => new JSONTypeHandler(),
				'null' => new NullTypeHandler());
		
		$this->aliases = array('us' => 'ustring', 'ustr' => 'ustring',
				's' => 'string', 'str' => 'string',
				'b' => 'boolean', 'bool' => 'boolean',
				'i' => 'integer', 'int' => 'integer',
				'double' => 'float', 'real' => 'float', 'f' => 'float',
				'x' => 'blob', 'bin' => 'blob',
				'dt' => 'DateTime', 'timestamp' => 'DateTime', 'datetime' => 'DateTime',
				'd' => 'date');
	}
	
	/**
	 * Associates a type handler to a given type
	 * @param string $type
	 * @param TypeHandler $typeHandler
	 * @throws \InvalidArgumentException
	 */
	public function setTypeHandler($type, TypeHandler $typeHandler) {
		if (!is_string($type) || empty($type)) {
			throw new \InvalidArgumentException("Type must be defined as a string");
		}
		
		$this->typeHandlers[$type] = $typeHandler;
	}
	
	/**
	 * Stores a new alias for a given type
	 * @param string $type
	 * @param string $alias
	 * @throws \InvalidArgumentException
	 */
	public function addAlias($type, $alias) {
		if (!is_string($type) || empty($type)) {
			throw new \InvalidArgumentException("Type must be defined as a string");
		}
		
		if (!is_string($alias) || empty($alias)) {
			throw new \InvalidArgumentException("Alias must be defined as a string");
		}
		
		$this->aliases[$alias] = $type;
	}
	
	/**
	 * Obtains the type handler assigned to a type
	 * @param string $type_or_alias
	 * @return boolean|TypeHandler
	 */
	public function getTypeHandler($type_or_alias) {
		$type_or_alias = $type_or_alias;
		
		//verify if its an alias
		if (array_key_exists($type_or_alias, $this->aliases)) {
			$type_or_alias = $this->aliases[$type_or_alias];
		}
		
		//check type existence
		if (!array_key_exists($type_or_alias, $this->typeHandlers)) {
			return false;
		}
		
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