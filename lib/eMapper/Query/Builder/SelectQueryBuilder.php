<?php
namespace eMapper\Query\Builder;

use eMapper\Reflection\Profile\ClassProfile;
use eMapper\Engine\Generic\Driver;
use eMapper\Query\Field;
use eMapper\Query\Aggregate\SQLFunction;
use eMapper\Reflection\Profile\Association\AbstractAssociation;

/**
 * The SelectQueryBuilder class generates SELECT queries for a given entity profile.
 * @author emaphp
 */
class SelectQueryBuilder extends QueryBuilder {
	/**
	 * SQL function
	 * @var SQLFunction
	 */
	protected $function;
	
	/**
	 * Association context
	 * @var AbstractAssociation
	 */
	protected $context;
	
	/**
	 * Sets the associated function for the current query
	 * @param SQLFunction $function
	 */
	public function setFunction(SQLFunction $function) {
		$this->function = $function;
	}
	
	public function setContext(AbstractAssociation $context) {
		$this->context = $context;
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
				if ($column instanceof Field) {
					$columns[] = empty($alias) ? $column->getColumnName($this->entity) : $alias . '.' . $column->getColumnName($this->entity);
				}
			}
			
			if (empty($columns)) {
				return empty($alias) ? '*' : "$alias.*";
			}
			
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
		
						if ($type == 'asc' || $type == 'desc') {
							$order_list[] = $column . ' ' . strtoupper($type);
						}
						else {
							$order_list[] = $column;
						}
					}
					else {
						$order_list[] = $column;
					}
				}
			}
		}
		
		if (empty($order)) {
			return '';
		}
		
		return 'ORDER BY ' . implode(', ', $order_list);
	}
	
	/**
	 * Returns the limit clause for the current query
	 * @param array $config
	 * @return string
	 */
	protected function getLimitClause($config) {
		if (array_key_exists('query.from', $config)) {
			if (array_key_exists('query.to', $config)) {
				return sprintf("LIMIT %d, %d", $config['query.from'], $config['query.to']);
			}
			else {
				return sprintf("LIMIT %d", $config['query.from']);
			}
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
		
	protected function joinTables($joins, $mainAlias) {
		$sql = '';
		
		foreach ($joins as $join) {
			list($assoc, $alias) = $join;
			$sql .= ' ' . $assoc->buildJoin($alias, $mainAlias);
		}
		
		return $sql;
	}
	
	public function build(Driver $driver, $config = null) {
		if (isset($this->context)) {
			$alias = '_t';
			$table = '@@' . $this->entity->getReferredTable() . " $alias";
			$reversedBy = $this->context->getReversedBy();
			$joins = [isset($reversedBy) ? $reversedBy : '__context__' => [$this->context, '_c']];
		}
		else {
			$alias = '';
			$table = '@@' . $this->entity->getReferredTable();
			$joins = [];
		}
		
		if (isset($this->function)) {
			$function = $this->function->getExpression($this->entity, $alias);
			
			if (array_key_exists('query.filter', $config) && !empty($config['query.filter'])) {
				$args = [];
				$filters = [];
					
				foreach ($config['query.filter'] as $filter) {
					if (!empty($alias)) $filter->setAlias($alias);
					$filters[] = $filter->evaluate($driver, $this->entity, $joins, $args);
				}

				$table .= $this->joinTables($joins, $alias);
				$condition = implode(' AND ', $filters);
				return [trim(sprintf("SELECT %s FROM %s WHERE %s", $function, $table, $condition)), $args];
			}
			
			return [trim(sprintf("SELECT %s FROM %s", $function, $table)), null];
		}
		
		$columns = $this->getColumns($config, $alias);
		
		if (array_key_exists('query.distinct', $config) && $config['query.distinct']) {
			$columns = 'DISTINCT ' . $columns;
		}
		
		$clauses = $this->getAdditionalClauses($config, $alias);
		
		if (isset($this->condition)) {
			$args = [];
			if (!empty($alias)) $condition->setAlias($alias);
			$condition = $this->condition->evaluate($driver, $this->entity, $joins, $args);

			$table .= $this->joinTables($joins, $alias);
			return [trim(sprintf("SELECT %s FROM %s WHERE %s %s", $columns, $table, $condition, $clauses)), $args];
		}
		elseif (array_key_exists('query.filter', $config) && !empty($config['query.filter'])) {
			$args = [];
			$filters = [];
				
			foreach ($config['query.filter'] as $filter) {
				if (!empty($alias)) $filter->setAlias($alias);
				$filters[] = $filter->evaluate($driver, $this->entity, $joins, $args);
			}
				
			$condition = implode(' AND ', $filters);
			$table .= $this->joinTables($joins, $alias);
			return [trim(sprintf("SELECT %s FROM %s WHERE %s %s", $columns, $table, $condition, $clauses)), $args];
		}
		
		return [trim(sprintf("SELECT %s FROM %s %s", $columns, $table, $clauses)), null];
	}
}

?>