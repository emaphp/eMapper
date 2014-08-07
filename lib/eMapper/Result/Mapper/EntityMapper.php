<?php
namespace eMapper\Result\Mapper;

use eMapper\Type\TypeManager;

class EntityMapper extends ObjectMapper {
	public function __construct(TypeManager $typeManager, $defaultClass) {
		ComplexMapper::__construct($typeManager, $defaultClass);
		$this->defaultClass = $defaultClass;
	}
}
?>