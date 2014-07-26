<?php
namespace eMapper\SQL\Builder;

use eMapper\Query\Predicate\Regex;
use eMapper\Query\Attr;

class RegexStatementBuilder extends StatementBuilder {
	public function build($matches = null) {
		$property = $matches[1];
		$negate = array_key_exists(2, $matches);
		$case_sensitive = !array_key_exists(3, $matches);
		
		$regex = new Regex(Attr::__callstatic($property), $case_sensitive, $negate);
		return $this->buildQuery(sprintf($regex->render(), $this->getColumnName($property)));
	}
}
?>