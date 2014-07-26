<?php
namespace eMapper\SQL\Builder;

use eMapper\Query\Predicate\GreaterThanEqual;
use eMapper\Query\Attr;
use eMapper\Query\Predicate\GreaterThan;

class GreaterThanStatementBuilder extends StatementBuilder {
	public function build($matches = null) {
		$property = $matches[1];
		$negate = array_key_exists(2, $matches) && !empty($matches[2]);
		
		//is an equal
		if (array_key_exists(3, $matches)) {
			$gt = new GreaterThanEqual(Attr::__callstatic($property), $negate);
		}
		else {
			$gt = new GreaterThan(Attr::__callstatic($property), $negate);
		}
		
		return $this->buildQuery(sprintf($gt->render(), $this->getColumnName($property), $this->getExpression($property)));
	}
}
?>