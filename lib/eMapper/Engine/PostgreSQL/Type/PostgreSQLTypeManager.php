<?php
namespace eMapper\Engine\PostgreSQL\Type;

use eMapper\Type\TypeManager;
use eMapper\Engine\PostgreSQL\Type\Handler\BlobTypeHandler;
use eMapper\Type\Handler\StringTypeHandler;
use eMapper\Type\Handler\BooleanTypeHandler;
use eMapper\Type\Handler\IntegerTypeHandler;
use eMapper\Type\Handler\FloatTypeHandler;
use eMapper\Type\Handler\DatetimeTypeHandler;
use eMapper\Type\Handler\DateTypeHandler;
use eMapper\Type\Handler\UnquotedStringTypeHandler;
use eMapper\Type\Handler\JSONTypeHandler;
use eMapper\Type\Handler\NullTypeHandler;

class PostgreSQLTypeManager extends TypeManager {
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
}
?>