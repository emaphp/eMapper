<?php
namespace eMapper\Query\Builder;

use eMapper\Engine\Generic\Driver;

/**
 * The UpdateQueryBuilder class generates UPDATE queries for a given entity profile.
 * @author emaphp
 */
class UpdateQueryBuilder extends QueryBuilder {
	public function build(Driver $driver, $config = null) {
		$table = '@@' . $this->entity->getReferredTable();
		$fields = implode(', ', $this->entity->fieldNames);
		$values = [];
		
		foreach ($this->entity->columnNames as $column => $field) {
			$type = $this->entity->getColumnType($column);
				
			if (isset($type)) {
				$values[] = $column . ' = #{' . $field . ':' . $type . '}';
			}
			else {
				$values[] = $column . ' = #{' . $field . '}';
			}
		}
		
		//evaluate condition
		$args = [];
		$condition = $this->condition->evaluate($driver, $this->entity, $args, 1);
		
		return [sprintf("UPDATE %s SET %s WHERE %s", $table, implode(', ', $values), $condition), $args];
	}
}
?>