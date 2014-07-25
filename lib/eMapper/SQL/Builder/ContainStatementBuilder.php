<?php
namespace eMapper\SQL\Builder;

use eMapper\Query\Predicate\Contains;

class ContainStatementBuilder extends StatementBuilder {
	public function build($matches = null) {
		$property = $matches[0];
		$negate = array_key_exists(2, $matches);
		$case_sensitive = !array_key_exists(3, $matches);
		
		//build condition
		$contains = new Contains(Attr::__callstatic($property), $case_sensitive, $negate);
		$condition = sprintf($contains->render(), $this->getColumnName($property));
		
		return sprintf("SELECT * FROM %s WHERE %s", $this->getTableName(), $condition);
	}
}
?>