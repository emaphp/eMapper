<?php
namespace eMapper\SQL\Builder;

use eMapper\Query\Predicate\Contains;
use eMapper\Query\Attr;

class ContainStatementBuilder extends StatementBuilder {
	public function build($matches = null) {
		$property = $matches[1];
		$negate = array_key_exists(2, $matches) && !empty($matches[2]);
		$case_sensitive = !array_key_exists(3, $matches) || empty($matches[3]);
		
		//build condition
		$contains = new Contains(Attr::__callstatic($property), $case_sensitive, $negate);
		return $this->buildQuery(sprintf($contains->render($this->driver), $this->getColumnName($property)));
	}
}
?>