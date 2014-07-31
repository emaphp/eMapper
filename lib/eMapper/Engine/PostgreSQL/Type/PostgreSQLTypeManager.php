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
use eMapper\Type\Handler\SafeStringTypeHandler;
use eMapper\Type\Handler\JSONTypeHandler;
use eMapper\Type\Handler\NullTypeHandler;

class PostgreSQLTypeManager extends TypeManager {
	public function __construct() {
		$this->typeHandlers = ['string' => new StringTypeHandler(),
							   'boolean' => new BooleanTypeHandler(),
							   'integer' => new IntegerTypeHandler(),
							   'float' => new FloatTypeHandler(),
							   'blob' => new BlobTypeHandler(),
							   'DateTime' => new DatetimeTypeHandler(),
							   'date' => new DateTypeHandler(),
							   'sstring' => new SafeStringTypeHandler(),
							   'json' => new JSONTypeHandler(),
							   'null' => new NullTypeHandler()];
	
		$this->aliases = ['ss' => 'sstring', 'sstr' => 'sstring',
						  's' => 'string', 'str' => 'string',
						  'b' => 'boolean', 'bool' => 'boolean',
						  'i' => 'integer', 'int' => 'integer',
						  'double' => 'float', 'real' => 'float', 'f' => 'float',
						  'x' => 'blob', 'bin' => 'blob',
						  'dt' => 'DateTime', 'timestamp' => 'DateTime', 'datetime' => 'DateTime',
						  'd' => 'date'];
	}
}
?>