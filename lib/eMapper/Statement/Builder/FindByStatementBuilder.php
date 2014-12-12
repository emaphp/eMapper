<?php
namespace eMapper\Statement\Builder;

use eMapper\Engine\Generic\Driver;

/**
 * The FindByStatementBuilder class builds a query string with a comparison predicate.
 * @author emaphp
 */
class FindByStatementBuilder extends StatementBuilder {
	public function build(Driver $driver, $matches = null) {
		$columns = $this->getColumnList();
		$table = $this->getTableName();
		$column = $this->getColumnName(strtolower($matches[1]));
		$expr = $this->getExpression(strtolower($matches[1]));
		return sprintf("SELECT %s FROM %s WHERE %s = %s", $columns, $table, $column, $expr);
	}
}