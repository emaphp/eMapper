<?php
namespace eMapper\SQL\Builder;

use eMapper\Query\Predicate\Range;
use eMapper\Query\Attr;

/**
 * The RangeStatementBuilder class builds a query string containing a Range predicate.
 * @author emaphp
 */
class RangeStatementBuilder extends StatementBuilder {
	public function build($matches = null) {
		$property = $matches[1];
		$negate = array_key_exists(2, $matches) && !empty($matches[2]);
		
		$range = new Range(Attr::__callstatic($property), $negate);
		return $this->buildQuery(sprintf($range->render($this->driver),$this->getColumnName($property), $this->getExpression($property), $this->getExpression($property, 1)));
	}
}
?>