<?php
namespace eMapper\SQL\Builder;

use eMapper\Query\Predicate\Equal;
use eMapper\Query\Attr;

class EqualStatementBuilder extends StatementBuilder {
	public function build($matches = null) {
		$property = $matches[1];
		$negate = array_key_exists(2, $matches);
		
		//build condition
		$eq = new Equal(Attr::__callstatic($property), $negate);
		$condition = sprintf($eq->render(), $this->getColumnName($property), $this->getExpression($property));
		return sprintf("SELECT * FROM %s WHERE %s", $this->getTableName(), $condition);
	}
}
?>