<?php
namespace eMapper\Query\Builder;

use eMapper\Engine\Generic\Driver;
use eMapper\Query\SQL\ORMTranslator;

/**
 * The UpdateQueryBuilder class generates UPDATE queries for a given entity profile.
 * @author emaphp
 */
class UpdateQueryBuilder extends QueryBuilder {
	public function build(Driver $driver, $config = null) {
		$table = '@@' . $this->entity->getReferredTable();
		$fields = implode(', ', $this->entity->getPropertyNames());
		$values = [];
		
		foreach ($this->entity->getColumnNames() as $column => $property) {
			$type = $this->entity->getProperty($property)->getType();
				
			if (isset($type))
				$values[] = $column . ' = #{' . $property . ':' . $type . '}';
			else
				$values[] = $column . ' = #{' . $property . '}';
		}
		
		//evaluate condition
		$args = new \ArrayObject();
		$joins = new \ArrayObject();
		$condition = $this->condition->evaluate(new ORMTranslator($this->entity), $driver, $args, $joins, 1);		
		return [sprintf("UPDATE %s SET %s WHERE %s", $table, implode(', ', $values), $condition), $args];
	}
}
?>