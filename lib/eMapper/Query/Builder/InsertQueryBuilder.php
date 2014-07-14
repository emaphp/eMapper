<?php

namespace eMapper\Query\Builder;

class InsertQueryBuilder extends QueryBuilder {
	public function build() {
		$table = $this->entity->getReferencedTable();
		$fields = implode(', ', $this->entity->fieldNames);
		$values = [];
		
		foreach ($this->entity->columnNames as $column => $field) {
			$type = $this->entity->getColumnType($column);
			
			if (isset($type)) {
				$values[] = '#{' . $field . ':' . $type . '}';
			}
			else {
				$values[] = '#{' . $field . '}';
			}
		}
		
		return [sprintf("INSERT INTO %s (%s) VALUES (%s)", $table, $fields, implode(', ', $values)), null];
	}
}
?>