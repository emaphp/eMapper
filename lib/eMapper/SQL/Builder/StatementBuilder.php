<?php
namespace eMapper\SQL\Builder;

use eMapper\Reflection\Profile\ClassProfile;
use eMapper\Engine\Generic\Driver;
use eMapper\Query\Attr;

/**
 * The StatementBuilder class implements the default behaviour required for statement builder classes.
 * @author emaphp
 */
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
	
	/**
	 * Builds a Statement instance
	 * @param array $matches
	 */
	public abstract function build($matches = null);
	
	/**
	 * Obtains the table name associated with the current entity
	 * @return string
	 */
	protected function getTableName() {
		return '@@' . $this->entity->getReferredTable();
	}
	
	/**
	 * Obtains the column name for the given property
	 * @param string $property
	 */
	protected function getColumnName($property) {
		$attr = Attr::__callstatic($property);
		return $attr->getColumnName($this->entity);
	}
	
	/**
	 * Returns the argument expression for the given property
	 * @param string $property
	 * @param int $argn
	 * @return string
	 */
	protected function getExpression($property, $argn = 0) {
		$type = $this->entity->getProperty($property)->getType();
		return isset($type) ? ('%{' . "$argn:$type" . '}') : ('%{' . $argn . '}') ;
	}
	
	/**
	 * Builds a query string with the given condition
	 * @param string $condition
	 * @return string
	 */
	protected function buildQuery($condition) {
		return sprintf("SELECT * FROM %s WHERE %s", $this->getTableName(), $condition);
	}
}
?>