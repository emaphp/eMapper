<?php
namespace eMapper\Statement\Builder;

use eMapper\SQL\Predicate\Regex;
use eMapper\Query\Attr;
use eMapper\Engine\Generic\Driver;

/**
 * The RegexStatementBuilder class builds a query string containing a Regex predicate.
 * @author emaphp
 */
class RegexStatementBuilder extends StatementBuilder {
	public function build(Driver $driver, $matches = null) {
		$property = $matches[1];
		$negate = array_key_exists(2, $matches) && !empty($matches[2]);
		$case_sensitive = !array_key_exists(3, $matches) || empty($matches[3]);
		
		$regex = new Regex(Attr::__callstatic($property), $case_sensitive, $negate);
		return $this->buildQuery(sprintf($regex->render($driver), $this->getColumnName($property)));
	}
}
?>