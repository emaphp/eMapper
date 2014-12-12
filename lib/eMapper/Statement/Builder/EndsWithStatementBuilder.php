<?php
namespace eMapper\Statement\Builder;

use eMapper\SQL\Predicate\EndsWith;
use eMapper\Query\Attr;
use eMapper\Engine\Generic\Driver;

/**
 * The EndsWithStatementBuilder class builds a query string containing a EndsWith predicate.
 * @author emaphp
 */
class EndsWithStatementBuilder extends StatementBuilder {
	public function build(Driver $driver, $matches = null) {
		$property = $matches[1];
		$negate = array_key_exists(2, $matches) && !empty($matches[2]);
		$case_sensitive = !array_key_exists(3, $matches) || empty($matches[3]);
		
		//build condition
		$endswith = new EndsWith(Attr::__callstatic($property), $case_sensitive, $negate);
		return $this->buildQuery(sprintf($endswith->generate($driver), $this->getColumnName($property)));
	}
}