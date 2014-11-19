<?php
namespace eMapper\Statement\Builder;

use eMapper\SQL\Predicate\GreaterThanEqual;
use eMapper\SQL\Predicate\GreaterThan;
use eMapper\Query\Attr;
use eMapper\Engine\Generic\Driver;

/**
 * The GreaterThanStatementBuilder class builds a query string containing a GreaterThan(Equal) predicate.
 * @author emaphp
 */
class GreaterThanStatementBuilder extends StatementBuilder {
	public function build(Driver $driver, $matches = null) {
		$property = $matches[1];
		$negate = array_key_exists(2, $matches) && !empty($matches[2]);
		
		//is an equal
		if (array_key_exists(3, $matches))
			$gt = new GreaterThanEqual(Attr::__callstatic($property), $negate);
		else
			$gt = new GreaterThan(Attr::__callstatic($property), $negate);
		
		return $this->buildQuery(sprintf($gt->render($driver), $this->getColumnName($property), $this->getExpression($property)));
	}
}
?>