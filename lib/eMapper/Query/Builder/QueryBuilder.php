<?php
namespace eMapper\Query\Builder;

use eMapper\Reflection\Profile\ClassProfile;
use eMapper\Query\Predicate\SQLPredicate;
use eMapper\Engine\Generic\Driver;

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
	
	public function setCondition(SQLPredicate $condition) {
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