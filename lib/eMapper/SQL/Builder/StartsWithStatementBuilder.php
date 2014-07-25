<?php
namespace eMapper\SQL\Builder;

use eMapper\Query\Predicate\StartsWith;
use eMapper\Query\Attr;
class StartsWithStatementBuilder extends StatementBuilder {
	public function build($matches = null) {
		$property = $matches[0];
		$negate = array_key_exists(2, $matches);
		$case_sensitive = !array_key_exists(3, $matches);
		
		//build condition
		$startswith = new StartsWith(Attr::__callstatic($property), $case_sensitive, $negate);
		$condition = sprintf($startswith->render(), $this->getColumnName($property));
		
		return sprintf("SELECT * FROM %s WHERE %s", $this->getTableName(), $condition);
	}
}
?>