<?php
namespace eMapper\SQL\Fluent;

use eMapper\Query\FluentQuery;
use eMapper\SQL\Predicate\SQLPredicate;
use eMapper\Query\Column;
use eMapper\SQL\Field\FluentFieldTranslator;

class SelectQuery extends AbstractQuery {	
	/**
	 * Columns to fetch
	 * @var array
	 */
	protected $columns;

	/**
	 * Order clauses
	 * @var array
	 */
	protected $orderByClause;
	
	/**
	 * Row limit
	 * @var int
	 */
	protected $limitClause;
	
	/**
	 * Row offset
	 * @var int
	 */
	protected $offsetClause;
	
	/**
	 * Group by clauses
	 * @var array
	 */
	protected $groupByClause;
	
	/**
	 * Having clause
	 * @var mixed
	 */
	protected $havingClause;
	
	/**
	 * Column translator
	 * @var FluentTranslator
	 */
	protected $translator;
	
	
	/**
	 * Sets columns to fetch
	 * @return \eMapper\SQL\Fluent\SelectQuery
	 */
	public function select() {
		$this->columns = func_get_args();
		return $this;
	}
	
	/**
	 * Sets order by clauses
	 * @return \eMapper\SQL\Fluent\SelectQuery
	 */
	public function orderBy() {
		$this->orderByClause = func_get_args();
		return $this;
	}
	
	/**
	 * Sets limit clause
	 * @param int $limit
	 * @return \eMapper\SQL\Fluent\SelectQuery
	 */
	public function limit($limit) {
		$this->limitClause = intval($limit);
		return $this;	
	}
	
	/**
	 * Sets offset clause
	 * @param int $offset
	 * @return \eMapper\SQL\Fluent\SelectQuery
	 */
	public function offset($offset) {
		$this->offsetClause = intval($offset);
		return $this;
	}
	
	/**
	 * Sets group by clauses
	 * @return \eMapper\SQL\Fluent\SelectQuery
	 */
	public function groupBy() {
		$this->groupByClause = func_get_args();
		return $this;
	}
	
	/**
	 * Sets the having clause
	 * @param string|SQLPredicate $column
	 * @return \eMapper\SQL\Fluent\SelectQuery
	 */
	public function having($column) {
		$this->havingClause = $column;
		return $this;
	}
	
	/**
	 * Retuns a column reference as a string
	 * @param string|Column $column
	 * @return string
	 */
	protected function translateColumn($column) {
		if ($column instanceof Column) {
			$_ = [];
			return $this->translator->translate($column, $_, $this->fromClause->getAlias());
		}
		elseif (is_string($column) && !empty($column))
			return $column;
		
		throw new \InvalidArgumentException("Columns must be specified either by a Column instance or a non-empty string");
	}
	
	/**
	 * Obtains an order column as a string
	 * @param string|Column $column
	 * @throws \UnexpectedValueException
	 * @return string
	 */
	protected function translateOrderColumn($column) {
		if ($column instanceof Column) {
			$path = $column->getPath();
			$type = $column->getType();
			$alias = $this->fromClause->getAlias();
			$tableList = $this->fromClause->getTableList();
				
			if (empty($path)) {
				if (is_null($alias))
					return empty($type) ? $column->getName() : $column->getName() . ' ' . $type;
				else
					return empty($type) ? $alias . '.' . $column->getName() : $alias . '.' . $column->getName() . ' ' . $type;
			}
				
			$references = $column->getPath()[0];
	
			if (!array_key_exists($references, $tableList))
				throw new \UnexpectedValueException("Column {$column->getName()} references an unknown table '$references'");
	
			return empty($type) ? $references . '.' . $column->getName() : $references . '.' . $column->getName() . ' ' . $type;
		}
		else
			return $this->translateColumn($column);
	}
	
	/**
	 * Retuns the columns to fetch as a string
	 * @return string
	 */
	protected function buildSelectClause() {
		if (empty($this->columns))
			return '*';
		
		$columns = [];
		
		foreach($this->columns as $column)
			$columns[] = $this->translateColumn($column);
		
		return implode(',', $columns);
	}
		
	/**
	 * Obtains additional clauses as a string
	 * @return string
	 */
	protected function buildAdditionalClauses() {
		$clauses = '';
		
		//group by
		if (!empty($this->groupBy)) {
			$group_by = [];
			
			foreach($this->groupBy as $group)
				$group_by[] = $this->translateColumn($group);
			
			$clauses .= 'GROUP BY ' . implode(',', $group_by) . ' ';
			
			//having
			if (isset($this->havingClause)) {
				$having = [];
				
				foreach ($this->havingClause as $have)
					$having[] = $this->translateColumn($have);
				
				$clauses .= 'HAVING ' . implode(',', $having) . ' ';
			}
		}
		
		//order by
		if (!empty($this->orderByClause)) {
			$order_by = [];
			
			foreach($this->orderByClause as $order)
				$order_by[] = $this->translateOrderColumn($order);
			
			$clauses .= 'ORDER BY ' . implode(',', $order_by) . ' ';
		}
		
		//limit
		if (isset($this->limitClause))
			$clauses .= "LIMIT {$this->limitClause} ";
		
		//offset
		if (isset($this->offsetClause))
			$clauses .= "OFFSET {$this->offsetClause}";
		
		return $clauses;
	}
	
	public function build() {
		//FROM clause
		$from = rtrim($this->fromClause->build());
		
		//create field translator from joined tables
		$this->translator = new FluentFieldTranslator($this->fromClause->getTableList());
		
		//SELECT clause
		$columns = $this->buildSelectClause();

		//WHERE clause
		$where = rtrim($this->buildWhereClause($this->translator));
		
		//etc...
		$clauses = $this->buildAdditionalClauses();
		
		if (!empty($where)) {
			$query = rtrim("SELECT $columns FROM $from WHERE $where $clauses");
		}
		
		$query = rtrim("SELECT $columns FROM $from $clauses");
		
		return [$query, [$this->fromClause->getArguments(), isset($this->whereClause) ? $this->whereClause->getArguments() : []]];
	}
	
	/**
	 * Fetchs the current query with an optional mapping type
	 * @param string $mapping_type
	 * @return mixed
	 */
	public function fetch($mapping_type = null) {
		list($query, $args) = $this->build();		
		$mapper = $this->fluent->getMapper();
		
		if (is_null($mapping_type))
			return $mapper->merge($this->config)->query($query);

		return $mapper->merge(array_merge($this->config, ['map.type' => $mapping_type]))->query($query);
	}
	
	public function getArguments() {
		return $this->args;
	}
}
?>