<?php
namespace eMapper\SQL\Builder;

use eMapper\Query\Attr;

/**
 * The FindByPkStatementBuilder class builds a query string with a comparison by primary key.
 * @author emaphp
 */
class FindByPkStatementBuilder extends StatementBuilder {
	public function build($matches = null) {
		$table = $this->getTableName();
		$column = $this->getColumnName($this->entity->primaryKey);
		$expr = $this->getExpression($this->entity->primaryKey);
		return sprintf("SELECT * FROM %s WHERE %s = %s", $table, $column, $expr);
	}
}
?>