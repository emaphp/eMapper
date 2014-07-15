<?php
namespace eMapper\Query\Builder;

use eMapper\Engine\Generic\Driver;

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
		$args = [];
		
		//get table name
		$table = $this->entity->getReferencedTable();
		
		//evaluate condition
		if ($this->truncate) {
			return [sprintf("DELETE FROM %s", $table), null];
		}
		elseif (isset($this->condition)) {
			$condition = $this->condition->evaluate($this->entity, $args);
		}
		elseif (array_key_exists('query.filter', $config) && !empty($config['query.filter'])) {
			$filters = [];
			
			foreach ($config['query.filter'] as $filter) {
				$filters[] = $filter->evaluate($driver, $this->entity, $args);
			}
			
			$condition = implode(' AND ', $filters);
		}
		
		if (isset($condition)) {
			return [sprintf("DELETE FROM %s WHERE %s", $table, $condition), $args];
		}
		
		throw new \RuntimeException("No condition specified for deletion query");
	}
}
?>