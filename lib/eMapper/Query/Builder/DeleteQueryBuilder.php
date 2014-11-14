<?php
namespace eMapper\Query\Builder;

use eMapper\Engine\Generic\Driver;
use eMapper\Reflection\Profile\ClassProfile;
use eMapper\Query\Predicate\Filter;
use eMapper\Query\SQL\ORMTranslator;

/**
 * The DeleteQueryBuilder class generates DELETE queries for a given entity profile.
 * @author emaphp
 */
class DeleteQueryBuilder extends QueryBuilder {
	/**
	 * Indicates if the current query deletes all entities
	 * @var boolean
	 */
	protected $truncate;
	
	public function __construct(ClassProfile $entity, $truncate = false) {
		parent::__construct($entity);
		$this->truncate = $truncate;
	}
	
	public function build(Driver $driver, $config = null) {
		$args = new \ArrayObject();
		$joins = new \ArrayObject();
		
		//get table name
		$table = '@@' . $this->entity->getReferredTable();
		
		//evaluate condition
		if ($this->truncate)
			return [sprintf("DELETE FROM %s", $table), null];
		elseif (isset($this->condition))
			$condition = $this->condition->evaluate(new ORMTranslator($this->entity), $driver, $args, $joins);
		elseif (array_key_exists('query.filter', $config) && !empty($config['query.filter'])) {
			$filter = new Filter($config['query.filter']);
			$condition = $filter->evaluate(new ORMTranslator($this->entity), $driver, $args, $joins);
		}
		
		if (isset($condition))
			return [sprintf("DELETE FROM %s WHERE %s", $table, $condition), $args];
		
		throw new \RuntimeException("No condition specified for deletion query");
	}
}
?>