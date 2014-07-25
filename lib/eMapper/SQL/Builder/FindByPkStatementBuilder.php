<?php
namespace eMapper\SQL\Builder;

use eMapper\Query\Attr;

class FindByPkStatementBuilder extends StatementBuilder {
	public function build($matches = null) {
		$table = $this->getTableName();
		$column = $this->getColumnName($this->entity->primaryKey);
		$expr = $this->getExpression($this->entity->primaryKey);
		return sprintf("SELECT * FROM %s WHERE %s = %s", $table, $column, $expr);
	}
}
?>