<?php
namespace eMapper\Query\Builder;

class DeleteQueryBuilder extends QueryBuilder {
	public function build() {
		//evaluate condition
		$args = [];
		$condition = $this->condition->evaluate($this->entity, $args);
		
		//get table name
		$table = $this->entity->getReferencedTable();
		
		return [sprintf("DELETE FROM %s WHERE %s", $table, $condition), $args];
	}
}
?>