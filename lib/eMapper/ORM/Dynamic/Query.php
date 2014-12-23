<?php
namespace eMapper\ORM\Dynamic;

use Omocha\AnnotationBag;
use eMapper\Mapper;

/**
 * The Query class implements the logic for evaluating queries against a list of arguments.
 * @author emaphp
 */
class Query extends DynamicAttribute {
	/**
	 * Query string
	 * @var string
	 */
	protected $query;
	
	protected function parseMetadata(AnnotationBag $propertyAnnotations) {
		$this->query = $propertyAnnotations->get('Query')->getValue();
	}
	
	public function evaluate($row, Mapper $mapper) {
		if ($this->evaluateCondition($row, $mapper->getConfig()) === false)
			return null;
		
		return $mapper->merge($this->config)->execute($this->query, $this->evaluateArguments($row));
	}
}