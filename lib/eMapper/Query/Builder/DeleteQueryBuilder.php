<?php
namespace eMapper\Query\Builder;

use eMapper\Engine\Generic\Driver;

class DeleteQueryBuilder extends QueryBuilder {
	public function build(Driver $driver, $config = null) {
		$args = [];
		
		//evaluate condition
		if (isset($this->condition)) {
			$condition = $this->condition->evaluate($this->entity, $args);
		}
		elseif (array_key_exists('query.filter', $config) && !empty($config['query.filter'])) {
			$filters = [];
			
			foreach ($config['query.filter'] as $filter) {
				$filters[] = $filter->evaluate($driver, $this->entity, $args);
			}
			
			$condition = implode(' AND ', $filters);
		}
		
		//get table name
		$table = $this->entity->getReferencedTable();
		
		return [sprintf("DELETE FROM %s WHERE %s", $table, $condition), $args];
	}
}
?>