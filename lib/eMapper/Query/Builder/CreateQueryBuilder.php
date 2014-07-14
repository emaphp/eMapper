<?php
namespace eMapper\Query\Builder;

class CreateQueryBuilder extends QueryBuilder {
	public function build() {
		$table = $this->entity->getReferencedTable();
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
		$condition = $this->condition->evaluate($this->entity, $args, 1);
		
		return [sprintf("UPDATE %s SET %s WHERE %s", $table, implode(', ', $values), $condition), $args];
	}
}
?>