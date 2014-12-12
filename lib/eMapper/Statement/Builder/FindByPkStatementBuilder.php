<?php
namespace eMapper\Statement\Builder;

use eMapper\Engine\Generic\Driver;

/**
 * The FindByPkStatementBuilder class builds a query string with a comparison by primary key.
 * @author emaphp
 */
class FindByPkStatementBuilder extends StatementBuilder {
	public function build(Driver $driver, $matches = null) {
		$columns = $this->getColumnList();
		$table = $this->getTableName();
		$column = $this->getColumnName($this->entity->getPrimaryKey());
		$expr = $this->getExpression($this->entity->getPrimaryKey());
		return sprintf("SELECT %s FROM %s WHERE %s = %s", $columns, $table, $column, $expr);
	}
}