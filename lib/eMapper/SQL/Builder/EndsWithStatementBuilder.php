<?php
namespace eMapper\SQL\Builder;

use eMapper\Query\Predicate\EndsWith;
use eMapper\Query\Attr;
class EndsWithStatementBuilder extends StatementBuilder {
	public function build($matches = null) {
		$property = $matches[0];
		$negate = array_key_exists(2, $matches);
		$case_sensitive = !array_key_exists(3, $matches);
		
		//build condition
		$endswith = new EndsWith(Attr::__callstatic($property), $case_sensitive, $negate);
		$condition = sprintf($endswith->render(), $this->getColumnName($property));
		
		return sprintf("SELECT * FROM %s WHERE %s", $this->getTableName(), $condition);
	}
}
?>