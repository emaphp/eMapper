<?php
namespace eMapper\SQL\Builder;

use eMapper\Query\Predicate\StartsWith;
use eMapper\Query\Attr;

class StartsWithStatementBuilder extends StatementBuilder {
	public function build($matches = null) {
		$property = $matches[0];
		$negate = array_key_exists(2, $matches) && !empty($matches[2]);
		$case_sensitive = !array_key_exists(3, $matches) || empty($matches[3]);
		
		//build condition
		$startswith = new StartsWith(Attr::__callstatic($property), $case_sensitive, $negate);
		return $this->buildQuery(sprintf($startswith->render($this->driver), $this->getColumnName($property)));
	}
}
?>