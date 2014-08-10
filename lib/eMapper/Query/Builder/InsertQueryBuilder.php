<?php
namespace eMapper\Query\Builder;

use eMapper\Engine\Generic\Driver;

/**
 * The InsertQueryBuilder class generates INSERT queries for a given entity profile.
 * @author emaphp
 */
class InsertQueryBuilder extends QueryBuilder {
	public function build(Driver $driver, $config = null) {
		$table = '@@' . $this->entity->getReferredTable();
		$fields = implode(', ', $this->entity->getPropertyNames());
		$values = [];
		
		foreach ($this->entity->getColumnNames() as $property) {
			$type = $this->entity->getProperty($property)->getType();
			
			if (isset($type)) {
				$values[] = '#{' . $property . ':' . $type . '}';
			}
			else {
				$values[] = '#{' . $property . '}';
			}
		}
		
		return [sprintf("INSERT INTO %s (%s) VALUES (%s)", $table, $fields, implode(', ', $values)), null];
	}
}
?>