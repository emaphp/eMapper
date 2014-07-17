<?php
namespace eMapper\Query\Builder;

use eMapper\Reflection\Profile\ClassProfile;
use eMapper\Engine\Generic\Driver;
use eMapper\Query\Field;

class SelectQueryBuilder extends QueryBuilder {
	/**
	 * Obtains columns expression for this query
	 * @param array $config
	 * @throws \RuntimeException
	 * @return string
	 */
	protected function getColumns($config) {
		if (array_key_exists('query.columns', $config) && !empty($config['query.columns'])) {
			$columns = [];
			
			foreach ($config['query.columns'] as $column) {
				if ($column instanceof Field) {
					$columns[] = $column->getColumnName($this->entity);
				}
			}
			
			if (empty($columns)) {
				return '*';
			}
			
			return implode(', ', $columns);
		}
		
		return '*';
	}
	
	/**
	 * Returns the order clause for the current query
	 * @param array $config
	 * @return string
	 */
	protected function getOrderClause($config) {
		$order_list = [];
		
		if (array_key_exists('query.order', $config)) {
			foreach ($config['query.order'] as $order) {
				if ($order instanceof Field) {
					$column = $order->getColumnName($this->entity);
						
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
	protected function getAdditionalClauses($config) {
		return trim(implode(' ', [$this->getOrderClause($config), $this->getLimitClause($config)]));
	}
	
	public function build(Driver $driver, $config = null) {
		$table = '@@' . $this->entity->getReferencedTable();
		
		$columns = $this->getColumns($config);
		
		if (array_key_exists('query.distinct', $config) && $config['query.distinct']) {
			$columns = 'DISTINCT ' . $columns;
		}
		
		$clauses = $this->getAdditionalClauses($config);
		
		if (isset($this->condition)) {
			$args = [];
			$condition = $this->condition->evaluate($driver, $this->entity, $args);
			return [trim(sprintf("SELECT %s FROM %s WHERE %s %s", $columns, $table, $condition, $clauses)), $args];
		}
		elseif (array_key_exists('query.filter', $config) && !empty($config['query.filter'])) {
			$args = [];
			$filters = [];
				
			foreach ($config['query.filter'] as $filter) {
				$filters[] = $filter->evaluate($driver, $this->entity, $args);
			}
				
			$condition = implode(' AND ', $filters);
			return [trim(sprintf("SELECT %s FROM %s WHERE %s %s", $columns, $table, $condition, $clauses)), $args];
		}
		
		return [trim(sprintf("SELECT %s FROM %s %s", $columns, $table, $clauses)), null];
	}
}

?>