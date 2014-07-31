<?php
namespace eMapper\Engine\PostgreSQL\Type\Handler;

use eMapper\Type\TypeHandler;
use eMapper\Type\ValueExport;

/**
 * @Safe
 */
class BlobTypeHandler extends TypeHandler {
	use ValueExport;
	
	public function getValue($value) {
		return pg_unescape_bytea($value);
	}
	
	public function castParameter($parameter) {
		if (($parameter = $this->toString($parameter)) === false) {
			return null;
		}
		
		return $parameter;
	}
	
	public function setParameter($parameter) {
		return "'\x" . bin2hex($parameter) . "'";
	}
}
?>