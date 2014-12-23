<?php
namespace eMapper\Fluent\Query\Clause;

/**
 * The HavingClause class is an abstraction of the sql HAVING clause
 * @author emaphp
 */
class HavingClause extends ArgumentClause {
	public function getName() {
		return 'HAVING';
	}
}