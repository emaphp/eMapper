<?php
namespace eMapper\SQL\Builder;

use eMapper\Query\Predicate\IsNull;
use eMapper\Query\Attr;
class IsNullStatementBuilder extends StatementBuilder {
	public function build($matches = null) {
		$property = $matches[1];
		$negate = array_key_exists(2, $matches) && !empty($matches[2]);
		
		$isnull = new IsNull(Attr::__callstatic($property), $negate);
		return $this->buildQuery(sprintf($isnull->render($this->driver), $this->getColumnName($property)));
	}
}
?>