<?php
namespace eMapper\Query\Builder;

use eMapper\Reflection\Profile\ClassProfile;
use eMapper\Engine\Generic\Driver;
use eMapper\Query\Field;
use eMapper\Query\Aggregate\SQLFunction;
use eMapper\Reflection\Profile\Association\Association;
use eMapper\Query\Predicate\Filter;
use eMapper\Query\Predicate\SQLPredicate;
use eMapper\Reflection\Profiler;
use eMapper\Reflection\Profile\Association\ManyToMany;
use eMapper\Query\Join;

/**
 * The SelectQueryBuilder class generates SELECT queries for a given entity profile.
 * @author emaphp
 */
class SelectQueryBuilder extends QueryBuilder {
	const DEFAULT_ALIAS = '_t';
	const CONTEXT_ALIAS = '_c';
	
	/**
	 * SQL function
	 * @var SQLFunction
	 */
	protected $function;
	
	/**
	 * Association context
	 * @var Association
	 */
	protected $association;
	
	/**
	 * Join condition
	 * @var SQLPredicate
	 */
	protected $joinCondition;
	
	/**
	 * Sets the associated function for the current query
	 * @param SQLFunction $function
	 */
	public function setFunction(SQLFunction $function) {
		$this->function = $function;
	}
	
	/**
	 * Sets the query context (called from AssociationManager)
	 * @param Association $association
	 * @param SQLPredicate $joinCondition
	 */
	public function setContext(Association $association, SQLPredicate $joinCondition) {
		$this->association = $association;
		$this->joinCondition = $joinCondition;
	}
	
	/**
	 * Obtains columns expression for this query
	 * @param array $config
	 * @param string $alias
	 * @throws \RuntimeException
	 * @return string
	 */
	protected function getColumns($config, $alias = '') {
		if (array_key_exists('query.columns', $config) && !empty($config['query.columns'])) {
			$columns = [];
			
			foreach ($config['query.columns'] as $column) {
				if ($column instanceof Field)
					$columns[] = empty($alias) ? $column->getColumnName($this->entity) : $alias . '.' . $column->getColumnName($this->entity);
			}
			
			if (empty($columns))
				return empty($alias) ? '*' : "$alias.*";
			
			return implode(', ', $columns);
		}
		
		return empty($alias) ? '*' : "$alias.*";
	}
	
	/**
	 * Returns the order clause for the current query
	 * @param array $config
	 * @return string
	 */
	protected function getOrderClause($config, $alias = '') {
		$order_list = [];
		
		if (array_key_exists('query.order', $config)) {
			foreach ($config['query.order'] as $order) {
				if ($order instanceof Field) {
					$column = empty($alias) ? $order->getColumnName($this->entity) : $alias . '.' . $order->getColumnName($this->entity);
						
					if ($order->hasType()) {
						$type = strtolower($order->getType());
		
						if ($type == 'asc' || $type == 'desc')
							$order_list[] = $column . ' ' . strtoupper($type);
						else
							$order_list[] = $column;
					}
					else
						$order_list[] = $column;
				}
			}
		}
		
		if (empty($order)) return '';
		return 'ORDER BY ' . implode(', ', $order_list);
	}
	
	/**
	 * Returns the limit clause for the current query
	 * @param array $config
	 * @return string
	 */
	protected function getLimitClause($config) {
		if (array_key_exists('query.from', $config)) {
			if (array_key_exists('query.to', $config))
				return sprintf("LIMIT %d, %d", $config['query.from'], $config['query.to']);
			else
				return sprintf("LIMIT %d", $config['query.from']);
		}
		
		return '';
	}
	
	/**
	 * Obtains order and limit expressions for this query
	 * @param array $config
	 * @throws \RuntimeException
	 * @return string
	 */
	protected function getAdditionalClauses($config, $alias = '') {
		return trim(implode(' ', [$this->getOrderClause($config, $alias), $this->getLimitClause($config)]));
	}
	
	/**
	 * Produces the SQL joins for this query
	 * @param array $joins
	 * @param string $mainAlias
	 * @param string $joinAlias
	 * @return string
	 */
	protected function joinTables($joins, $mainAlias, $joinAlias) {
		$sql = '';
		$left_join = count($joins) > 1;
		
		if ($this->association instanceof ManyToMany)
			$sql .= ' ' . $this->association->buildAssociationJoin($joinAlias, $mainAlias) . ' ';
		
		foreach ($joins as $join) {
			if ($left_join) $join->setType(Join::LEFT);
			$sql .= ' ' . $join->toSQL($mainAlias);
		}
		
		return $sql;
	}
	
	public function build(Driver $driver, $config = null) {
		$alias = $joinAlias = self::DEFAULT_ALIAS;
		$table = '@@' . $this->entity->getReferredTable() . ' ' . $alias;
		$joins = [];
		
		//check for many-to-many association (requires adding a join)
		if ($this->association instanceof ManyToMany)
			$joinAlias = self::CONTEXT_ALIAS;
		
		//call function
		if (isset($this->function)) {
			$function = $this->function->getExpression($this->entity, $alias);

			if (array_key_exists('query.filter', $config) && !empty($config['query.filter'])) {
				$args = [];
				$filters = [];
				
				if (isset($this->association)) {
					$this->joinCondition->setAlias($joinAlias);
					$filters[] = $this->joinCondition->evaluate($driver, $this->entity, $joins, $args);
				}
				
				foreach ($config['query.filter'] as $filter) {
					$filter->setAlias($alias);
					$filters[] = $filter->evaluate($driver, $this->entity, $joins, $args);
				}

				$table .= $this->joinTables($joins, $alias, $joinAlias);
				$condition = implode(' AND ', $filters);
				return [trim(sprintf("SELECT %s FROM %s WHERE %s", $function, $table, $condition)), $args];
			}
			
			return [trim(sprintf("SELECT %s FROM %s", $function, $table)), null];
		}
		
		//get columns
		$columns = $this->getColumns($config, $alias);
		
		if (array_key_exists('query.distinct', $config) && $config['query.distinct']) {
			$columns = 'DISTINCT ' . $columns;
		}
		
		//get additional clauses
		$clauses = $this->getAdditionalClauses($config, $alias);
		
		if (isset($this->condition)) {
			$args = [];

			if (isset($this->association)) {
				$this->joinCondition->setAlias($joinAlias);
				$this->condition->setAlias($alias);
				$filter = new Filter([$this->joinCondition, $this->condition]);
				$condition = $filter->evaluate($driver, $this->entity, $joins, $args);
			}
			else {
				$this->condition->setAlias($alias);
				$condition = $this->condition->evaluate($driver, $this->entity, $joins, $args);
			}
			
			$table .= $this->joinTables($joins, $alias, $joinAlias);
			return [trim(sprintf("SELECT %s FROM %s WHERE %s %s", $columns, $table, $condition, $clauses)), $args];
		}
		elseif (array_key_exists('query.filter', $config) && !empty($config['query.filter'])) {
			$args = [];
			$filters = [];
			
			if (isset($this->association)) {
				$this->joinCondition->setAlias($joinAlias);
				$filters[] = $this->joinCondition->evaluate($driver, $this->entity, $joins, $args);
			}
			
			foreach ($config['query.filter'] as $filter) {
				$filter->setAlias($alias);
				$filters[] = $filter->evaluate($driver, $this->entity, $joins, $args);
			}
				
			$condition = implode(' AND ', $filters);
			$table .= $this->joinTables($joins, $alias, $joinAlias);
			return [trim(sprintf("SELECT %s FROM %s WHERE %s %s", $columns, $table, $condition, $clauses)), $args];
		}
		
		if (isset($this->association)) {
			$this->joinCondition->setAlias($joinAlias);
			$condition = $this->joinCondition->evaluate($driver, $this->entity, $joins, $args);
			$table .= $this->joinTables($joins, $alias, $joinAlias);
			return [trim(sprintf("SELECT %s FROM %s WHERE %s %s", $columns, $table, $condition, $clauses)), $args];
		}
		
		return [trim(sprintf("SELECT %s FROM %s %s", $columns, $table, $clauses)), null];
	}
}

?>