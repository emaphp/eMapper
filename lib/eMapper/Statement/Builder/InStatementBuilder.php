<?php
namespace eMapper\Statement\Builder;

use eMapper\SQL\Predicate\In;
use eMapper\Query\Attr;
use eMapper\Engine\Generic\Driver;

/**
 * The InStatementBuilder class builds a query string containing a In predicate.
 * @author emaphp
 */
class InStatementBuilder extends StatementBuilder {
	public function build(Driver $driver, $matches = null) {
		$property = $matches[1];
		$negate = array_key_exists(2, $matches) && !empty($matches[2]);
		
		$in = new In(Attr::__callstatic($property), $negate);
		return $this->buildQuery(sprintf($in->generate($driver), $this->getColumnName($property), $this->getExpression($property)));
	}
}