<?php
namespace eMapper\Query\Builder;

use eMapper\Engine\Generic\Driver;

class DeleteQueryBuilder extends QueryBuilder {
	public function build(Driver $driver, $config = null) {
		//evaluate condition
		$args = [];
		$condition = $this->condition->evaluate($this->entity, $args);
		
		//get table name
		$table = $this->entity->getReferencedTable();
		
		return [sprintf("DELETE FROM %s WHERE %s", $table, $condition), $args];
	}
}
?>