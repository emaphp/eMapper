<?php
namespace eMapper\Query\Builder;

use eMapper\Reflection\Profile\ClassProfile;
use eMapper\Query\Predicate\SQLPredicate;
use eMapper\Engine\Generic\Driver;

/**
 * A QueryBuilder class encapsulates the generic behaviour for building queries for a given entity.
 * @author emaphp
 */
abstract class QueryBuilder {
	/**
	 * Entity profile
	 * @var ClassProfile
	 */
	protected $entity;
	
	/**
	 * Query condition
	 * @var SQLPredicate
	 */
	protected $condition;
	
	public function __construct(ClassProfile $entity) {
		$this->entity = $entity;
	}
	
	/**
	 * Sets the associated condition for the current query
	 * @param SQLPredicate $condition
	 */
	public function setCondition(SQLPredicate $condition = null) {
		$this->condition = $condition;
	}

	/**
	 * Builds the query
	 * @param Driver $driver
	 * @param array $config
	 */
	public abstract function build(Driver $driver, $config = null);
}
?>