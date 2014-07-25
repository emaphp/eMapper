<?php
namespace eMapper\SQL\Builder;

use eMapper\Query\Predicate\In;
use eMapper\Query\Attr;

class InStatementBuilder extends StatementBuilder {
	public function build($matches = null) {
		$property = $matches[1];
		$negate = array_key_exists(2, $matches);
		
		$in = new In(Attr::__callstatic($property), $negate);
		$condition = sprintf($in->render(), $this->getColumnName($property), $this->getExpression($property));
		
		return sprintf("SELECT * FROM %s WHERE %s", $this->getTableName(), $condition);
	}
}
?>