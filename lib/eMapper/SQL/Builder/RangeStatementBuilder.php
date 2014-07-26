<?php
namespace eMapper\SQL\Builder;

use eMapper\Query\Predicate\Range;
use eMapper\Query\Attr;

class RangeStatementBuilder extends StatementBuilder {
	public function build($matches = null) {
		$property = $matches[1];
		$negate = array_key_exists(2, $matches);
		
		$range = new Range(Attr::__callstatic($property), $negate);
		return $this->buildQuery(sprintf($range->render(), $this->getExpression($property), $this->getExpression($property, 1)));
	}
}
?>