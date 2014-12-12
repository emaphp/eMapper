<?php
namespace eMapper\Statement\Builder;

use eMapper\SQL\Predicate\Range;
use eMapper\Query\Attr;
use eMapper\Engine\Generic\Driver;

/**
 * The RangeStatementBuilder class builds a query string containing a Range predicate.
 * @author emaphp
 */
class RangeStatementBuilder extends StatementBuilder {
	public function build(Driver $driver, $matches = null) {
		$property = $matches[1];
		$negate = array_key_exists(2, $matches) && !empty($matches[2]);
		
		$range = new Range(Attr::__callstatic($property), $negate);
		return $this->buildQuery(sprintf($range->generate($driver),$this->getColumnName($property), $this->getExpression($property), $this->getExpression($property, 1)));
	}
}