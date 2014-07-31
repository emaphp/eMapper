<?php
namespace eMapper\SQL\Builder;

use eMapper\Query\Predicate\In;
use eMapper\Query\Attr;

/**
 * The InStatementBuilder class builds a query string containing a In predicate.
 * @author emaphp
 */
class InStatementBuilder extends StatementBuilder {
	public function build($matches = null) {
		$property = $matches[1];
		$negate = array_key_exists(2, $matches) && !empty($matches[2]);
		
		$in = new In(Attr::__callstatic($property), $negate);
		return $this->buildQuery(sprintf($in->render($this->driver), $this->getColumnName($property), $this->getExpression($property)));
	}
}
?>