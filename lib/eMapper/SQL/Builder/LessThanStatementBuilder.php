<?php
namespace eMapper\SQL\Builder;

use eMapper\Query\Predicate\LessThanEqual;
use eMapper\Query\Attr;
use eMapper\Query\Predicate\LessThan;

class LessThanStatementBuilder extends StatementBuilder {
	public function build($matches = null) {
		$property = $matches[1];
		$negate = array_key_exists(2, $matches) && !empty($matches[2]);
		
		//is an equal
		if (array_key_exists(3, $matches)) {
			$lt = new LessThanEqual(Attr::__callstatic($property), $negate);
		}
		else {
			$lt = new LessThan(Attr::__callstatic($property), $negate);
		}
		
		$condition = sprintf($lt->render(), $this->getColumnName($property), $this->getExpression($property));
		return sprintf("SELECT * FROM %s WHERE %s", $this->getTableName(), $condition);
	}
}
?>