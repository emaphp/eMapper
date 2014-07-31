<?php
namespace eMapper\SQL\Builder;

/**
 * The FindByStatementBuilder class builds a query string with a comparison predicate.
 * @author emaphp
 */
class FindByStatementBuilder extends StatementBuilder {
	public function build($matches = null) {
		$table = $this->getTableName();
		$column = $this->getColumnName(strtolower($matches[1]));
		$expr = $this->getExpression(strtolower($matches[1]));

		return sprintf("SELECT * FROM %s WHERE %s = %s", $table, $column, $expr);
	}
}
?>