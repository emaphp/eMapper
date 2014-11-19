<?php
namespace eMapper\Statement\Builder;

use eMapper\SQL\Predicate\IsNull;
use eMapper\Query\Attr;
use eMapper\Engine\Generic\Driver;

/**
 * The IsNullStatementBuilder class builds a query string containing a IsNull predicate.
 * @author emaphp
 */
class IsNullStatementBuilder extends StatementBuilder {
	public function build(Driver $driver, $matches = null) {
		$property = $matches[1];
		$negate = array_key_exists(2, $matches) && !empty($matches[2]);
		
		$isnull = new IsNull(Attr::__callstatic($property), $negate);
		return $this->buildQuery(sprintf($isnull->render($driver), $this->getColumnName($property)));
	}
}
?>