<?php
namespace eMapper\Statement\Builder;

use eMapper\SQL\Predicate\StartsWith;
use eMapper\Query\Attr;
use eMapper\Engine\Generic\Driver;

/**
 * The StartsWithStatementBuilder class builds a query string containing a StartsWith predicate.
 * @author emaphp
 */
class StartsWithStatementBuilder extends StatementBuilder {
	public function build(Driver $driver, $matches = null) {
		$property = $matches[1];
		$negate = array_key_exists(2, $matches) && !empty($matches[2]);
		$case_sensitive = !array_key_exists(3, $matches) || empty($matches[3]);
		
		//build condition
		$startswith = new StartsWith(Attr::__callstatic($property), $case_sensitive, $negate);
		return $this->buildQuery(sprintf($startswith->generate($driver), $this->getColumnName($property)));
	}
}