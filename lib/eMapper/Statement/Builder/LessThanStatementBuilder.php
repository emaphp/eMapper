<?php
namespace eMapper\Statement\Builder;

use eMapper\SQL\Predicate\LessThanEqual;
use eMapper\SQL\Predicate\LessThan;
use eMapper\Query\Attr;
use eMapper\Engine\Generic\Driver;

/**
 * The LessThanStatementBuilder class builds a query string containinf a LessThan(Equal) predicate.
 * @author emaphp
 */
class LessThanStatementBuilder extends StatementBuilder {
	public function build(Driver $driver, $matches = null) {
		$property = $matches[1];
		$negate = array_key_exists(2, $matches) && !empty($matches[2]);
		
		//is an equal
		if (array_key_exists(3, $matches))
			$lt = new LessThanEqual(Attr::__callstatic($property), $negate);
		else
			$lt = new LessThan(Attr::__callstatic($property), $negate);
		
		return $this->buildQuery(sprintf($lt->render($driver), $this->getColumnName($property), $this->getExpression($property)));
	}
}
?>