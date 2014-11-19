<?php
namespace eMapper\Statement\Builder;

use eMapper\SQL\Predicate\Equal;
use eMapper\Query\Attr;
use eMapper\Engine\Generic\Driver;

/**
 * The EqualStatementBuilder class builds a query string containing an Equal predicate.
 * @author emaphp
 */
class EqualStatementBuilder extends StatementBuilder {
	public function build(Driver $driver, $matches = null) {
		$property = $matches[1];
		$negate = array_key_exists(2, $matches) && !empty($matches[2]);		
		
		//build condition
		$eq = new Equal(Attr::__callstatic($property), $negate);
		return $this->buildQuery(sprintf($eq->render($driver), $this->getColumnName($property), $this->getExpression($property)));
	}
}
?>