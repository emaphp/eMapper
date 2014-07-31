<?php
namespace eMapper\SQL\Builder;

/**
 * The FindAllStatementBuilder class builds a query string to obtain all rows for a given entity.
 * @author emaphp
 */
class FindAllStatemetBuilder extends StatementBuilder {
	public function build($matches = null) {
		return sprintf("SELECT * FROM %s", $this->getTableName());
	}
}
?>