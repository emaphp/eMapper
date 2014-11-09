<?php
namespace eMapper\Reflection\Profile\Dynamic;

use Omocha\AnnotationBag;
use eMapper\Mapper;

/**
 * The QueryCallback class implements the logic for evaluating queries againts
 * a list of arguments.
 * @author emaphp
 */
class QueryCallback extends DynamicAttribute {
	/**
	 * Raw query
	 * @var string
	 */
	protected $query;
	
	protected function parseMetadata(AnnotationBag $annotations) {
		//obtain query
		$this->query = $annotations->get('Query')->getValue();
	}
	
	public function evaluate($row, Mapper $mapper) {
		//evaluate condition
		if ($this->checkCondition($row, $mapper->getConfig()) === false) return null;

		//build argument list
		$args = $this->evaluateArgs($row);
		array_unshift($args, $this->query);

		//invoke statement
		return call_user_func_array([$mapper->merge($this->config), 'query'], $args);
	}
}
?>