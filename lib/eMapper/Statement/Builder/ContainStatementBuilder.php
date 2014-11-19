<?php
namespace eMapper\Statement\Builder;

use eMapper\SQL\Predicate\Contains;
use eMapper\Query\Attr;
use eMapper\Engine\Generic\Driver;

/**
 * The ContainStatementBuilder class builds a query string containing a Contains predicate.
 * @author emaphp
 */
class ContainStatementBuilder extends StatementBuilder {
	public function build(Driver $driver, $matches = null) {
		$property = $matches[1];
		$negate = array_key_exists(2, $matches) && !empty($matches[2]);
		$case_sensitive = !array_key_exists(3, $matches) || empty($matches[3]);
		
		//build condition
		$contains = new Contains(Attr::__callstatic($property), $case_sensitive, $negate);
		return $this->buildQuery(sprintf($contains->render($driver), $this->getColumnName($property)));
	}
}
?>