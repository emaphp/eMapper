<?php
namespace eMapper\Query\Builder;

use eMapper\Reflection\Profile\ClassProfile;
use eMapper\Engine\Generic\Driver;

class SelectQueryBuilder extends QueryBuilder {
	/**
	 * Obtains columns expression for this query
	 * @param array $config
	 * @throws \RuntimeException
	 * @return string
	 */
	protected function getColumns($config) {
		$columns = '*';
		
		if (array_key_exists('query.columns', $config)) {
			if (!empty($config['query.columns'])) {
				return implode(', ', $config['query.columns']);
			}
		}
		elseif (array_key_exists('query.attrs', $config)) {
			if (!empty($config['query.attrs'])) {
				$columns = [];
		
				foreach ($config['query.attrs'] as $attr) {
					if (!array_key_exists($attr, $this->entity->fieldNames)) {
						throw new \RuntimeException(sprintf("Attribute $attr not declared in class %s", $this->entity->reflectionClass->getName()));
					}
					
					//get column name
					$columns[] = $this->entity->fieldNames[$attr];
				}
		
				return implode(', ', $columns);
			}
		}
		
		return $columns;
	}
	
	/**
	 * Obtains order and limit expressions for this query
	 * @param array $config
	 * @throws \RuntimeException
	 * @return string
	 */
	protected function getAdditionalClauses($config) {
		$clauses = [];
		
		//add order
		if (array_key_exists('query.order_by', $config)) {
			$order_by = 'ORDER BY';
		
			foreach ($config['query.order_by'] as $order) {
				$regex = '/^([\w]+)\s+([ASC|DESC])$/';
		
				if (preg_match($regex, $order, $matches)) {
					if (!array_key_exists($matches[1], $this->entity->fieldNames)) {
						throw new \RuntimeException();
					}
		
					$column = $this->entity->fieldNames[$matches[1]] . ' ' . $matches[2];
				}
				else {
					if (!array_key_exists($order, $this->entity->fieldNames)) {
						throw new \RuntimeException();
					}
		
					$column = $this->entity->fieldNames[$order];
				}
		
				$order_by .= " $column,";
			}
		
			$clauses[] = substr($order_by, 0, -1);
		}
		
		//add limit
		if (array_key_exists('query.left_limit', $config)) {
			if (array_key_exists('query.right_limit', $config)) {
				$clauses[] = sprintf("LIMIT %d, %d", $config['query.left_limit'], $config['query.right_limit']);
			}
			else {
				$clauses[] = sprintf("LIMIT %d", $config['query.left_limit']);
			}
		}
		
		return implode(' ', $clauses);
	}
	
	public function build(Driver $driver, $config = null) {
		$table = $this->entity->getReferencedTable();
		
		$columns = $this->getColumns($config);
		
		if (array_key_exists('query.distinct', $config) && $config['query.distinct']) {
			$columns = 'DISTINCT ' . $columns;
		}
		
		$clauses = $this->getAdditionalClauses($config);
		
		if (isset($this->condition)) {
			$args = [];
			$condition = $this->condition->evaluate($driver, $this->entity, $args);
			return [sprintf("SELECT %s FROM %s WHERE %s %s", $columns, $table, $condition, $clauses), $args];
		}
		elseif (array_key_exists('query.filter', $config) && !empty($config['query.filter'])) {
			$args = [];
			$filters = [];
				
			foreach ($config['query.filter'] as $filter) {
				$filters[] = $filter->evaluate($driver, $this->entity, $args);
			}
				
			$condition = implode(' AND ', $filters);
			return [sprintf("SELECT %s FROM %s WHERE %s %s", $columns, $table, $condition, $clauses), $args];
		}
		
		return [sprintf("SELECT %s FROM %s %s", $columns, $table, $clauses), null];
	}
}

?>