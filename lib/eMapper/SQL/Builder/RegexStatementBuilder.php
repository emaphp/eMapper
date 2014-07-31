<?php
namespace eMapper\SQL\Builder;

use eMapper\Query\Predicate\Regex;
use eMapper\Query\Attr;

/**
 * The RegexStatementBuilder class builds a query string containing a Regex predicate.
 * @author emaphp
 */
class RegexStatementBuilder extends StatementBuilder {
	public function build($matches = null) {
		$property = $matches[1];
		$negate = array_key_exists(2, $matches) && !empty($matches[2]);
		$case_sensitive = !array_key_exists(3, $matches) || empty($matches[3]);
		
		$regex = new Regex(Attr::__callstatic($property), $case_sensitive, $negate);
		return $this->buildQuery(sprintf($regex->render($this->driver), $this->getColumnName($property)));
	}
}
?>