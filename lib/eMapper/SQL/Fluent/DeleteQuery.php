<?php
namespace eMapper\SQL\Fluent;

class DeleteQuery extends AbstractQuery {	
	protected function buildFromClause() {
		return isset($this->alias) ? $this->table . ' ' . $this->alias : $this->table;
	}
	
	public function build() {
		$from = $this->buildFromClause();
		$where = $this->buildWhereClause();
		return ["DELETE $from WHERE $where", $this->whereClause->getArguments()];
	}
}
?>