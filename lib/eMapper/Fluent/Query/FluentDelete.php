<?php
namespace eMapper\Fluent\Query;

use eMapper\SQL\Predicate\SQLPredicate;
use eMapper\Query\Schema;

/**
 * The FluentDelete class provides a fluent interface for building DELETE queries.
 * @author emaphp
 */
class FluentDelete extends AbstractQuery {	
	public function build() {
		//create query schema
		$schema = new Schema($this->getFluent()->getEntityProfile());
		
		//WHERE clause
		$where = null;
		if (isset($this->whereClause))
			$where = $this->whereClause->build($schema);
		
		//update schema
		$this->updateSchema($schema);
		
		//FROM clause
		$from = rtrim($this->fromClause->build($this, $schema));
		
		//build query structure
		$query = empty($where) ? rtrim("DELETE FROM $from") : rtrim("DELETE FROM $from WHERE $where");
		
		//generate query arguments
		$args = [];
		
		//obtain arguments in WHERE clause
		if (isset($this->whereClause) && $this->whereClause->hasArguments())
			$args = $this->whereClause->getArguments();
		
		//get generated arguments
		$complexArg = $schema->getArguments();
		
		//append complexArg to argument list if necessary
		if (!empty($complexArg))
			array_unshift($args, $complexArg);
		
		return [$query, $args];
	}
}