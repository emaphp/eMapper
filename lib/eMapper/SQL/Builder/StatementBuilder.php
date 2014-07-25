<?php
namespace eMapper\SQL\Builder;

use eMapper\Reflection\Profile\ClassProfile;
use eMapper\Engine\Generic\Driver;
use eMapper\Query\Attr;

abstract class StatementBuilder {
	/**
	 * Engine driver
	 * @var Driver
	 */
	protected $driver;
	
	/**
	 * Entity profile
	 * @var ClassProfile
	 */
	protected $entity;

	public function __construct(Driver $driver, ClassProfile $entity) {
		$this->driver = $driver;
		$this->entity = $entity;
	}
	
	public abstract function build($matches = null);
	
	protected function getTableName() {
		return '@@' . $this->entity->getReferredTable();
	}
	
	protected function getColumnName($property) {
		$attr = Attr::__callstatic($property);
		return $attr->getColumnName($this->entity);
	}
	
	protected function getExpression($property) {
		$type = $this->entity->getFieldType($property);
		return isset($type) ? "{0:$type}" : '{0}';
	}
}
?>