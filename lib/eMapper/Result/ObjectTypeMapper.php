<?php
namespace eMapper\Result;

use eMapper\Type\TypeHandler;

interface ObjectTypeMapper {
	public function mapResult($result);
	public function mapList($result, $index = null, $type = null);
}
?>