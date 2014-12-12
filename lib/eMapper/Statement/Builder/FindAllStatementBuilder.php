<?php
namespace eMapper\Statement\Builder;

use eMapper\Engine\Generic\Driver;

/**
 * The FindAllStatementBuilder class builds a query string to obtain all rows for a given entity.
 * @author emaphp
 */
class FindAllStatementBuilder extends StatementBuilder {
	public function build(Driver $driver, $matches = null) {
		return sprintf("SELECT %s FROM %s", $this->getColumnList(), $this->getTableName());
	}
}