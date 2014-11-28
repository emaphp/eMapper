<?php
namespace eMapper\SQL\Fluent;

use eMapper\SQL\Predicate\SQLPredicate;
use eMapper\SQL\Field\FluentFieldTranslator;

/**
 * The FluentDelete class provides a fluent interface for building DELETE queries
 * @author emaphp
 */
class FluentDelete extends AbstractFluentQuery {	
	public function build() {
		//FROM clause
		$from = rtrim($this->fromClause->build());
		$fromArgs = $this->fromClause->getArguments();
		
		//create field translator from joined tables
		$this->translator = new FluentFieldTranslator($this->fromClause->getTableList());
		
		//WHERE clause
		$where = rtrim($this->buildWhereClause());
		
		//build query structure
		$query = empty($where) ? rtrim("DELETE FROM $from") : rtrim("DELETE FROM $from WHERE $where");
		
		//generate query arguments
		$args = [];
		$counter = 0;
		$complexArg = !empty($fromArgs) ? $fromArgs : [];

		//append arguments in WHERE clause
		if (isset($this->whereClause)) {
			$whereArgs = $this->whereClause->getArguments();
			
			if ($this->whereClause->getClause() instanceof SQLPredicate)
				$complexArg = array_merge($whereArgs, $complexArg);
			elseif (!empty($whereArgs)) {
				foreach ($whereArgs as $arg)
					$args[$counter++] = $arg;
			}
		}
		
		//append complexArg to argument list if necessary
		if (!empty($complexArg))
			array_unshift($args, $complexArg);
		
		return [$query, $args];
	}
}
?>