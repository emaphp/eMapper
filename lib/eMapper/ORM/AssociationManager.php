<?php
namespace eMapper\ORM;

use eMapper\Mapper;
use eMapper\ORM\Association\Association;
use eMapper\SQL\Predicate\SQLPredicate;
use eMapper\Reflection\Profiler;

/**
 * The AssociationManager class is aimed to fetch a result from an existing association between 2 entity classes.
 * @author emaphp
 */
class AssociationManager extends Manager {
	/**
	 * Association context
	 * @var \eMapper\Reflection\Association\Association
	 */
	protected $association;
	
	/**
	 * Join predicate
	 * @var \eMapper\SQL\Predicate\SQLPredicate
	 */
	protected $predicate;
		
	public function __construct(Mapper $mapper, Association $association, SQLPredicate $predicate) {
		$this->mapper = $mapper;
		$this->association = $association;
		$this->predicate = $predicate;
		$this->entityProfile = Profiler::getClassProfile($association->getEntityClass());
		$this->preserveInstance = true;
		
		//store additional options
		$index = $association->getIndex();
		if (!empty($index))
			$this->setOption('query.index', $index);
		
		$order = $association->getOrder();
		if (!empty($order))
			$this->setOption('query.orderBy', $order);
		
		$cache = $association->getCache();
		if (!empty($cache)) {
			$this->setOption('cache.ttl', intval($cache->getArgument()));
			$this->setOption('cache.key', $cache->getValue());
		}
	}
	
	/**
	 * Fetchs related data
	 */
	public function fetch() {
		return $this->association->fetchValue($this);
	}
	
	public function find(SQLPredicate $condition = null) {
		$this->mapper->connect();
		
		//build fluent query
		$query = $this->mapper->newQuery($this->entityProfile);
		$query->from($this->entityProfile->getEntityTable(), self::DEFAULT_ALIAS);
		
		//append call to required join
		$this->association->appendJoin($query, self::DEFAULT_ALIAS, self::CONTEXT_ALIAS);
		
		//set condition		
		$args = func_get_args();
		if (empty($args) && $this->hasOption('query.filter'))
			$query->where($this->getFilter(), $this->predicate);
		elseif (isset($condition))
			$query->where(new Filter($args), $this->predicate);
		
		//order by
		if ($this->hasOption('query.order'))
			$query->orderBy($this->getOrderBy());
		
		//build query
		list($sql, $args) = $query->build();
		return $this->mapper->merge($this->clean(['map.type' => $this->getListMappingExpression()]))->execute($sql, $args);
	}
	
	public function get(SQLPredicate $condition = null) {
		$this->mapper->connect();
	
		//build fluent query
		$query = $this->mapper->newQuery($this->entityProfile);
		$query->from($this->entityProfile->getEntityTable(), self::DEFAULT_ALIAS);
	
		//append call to required join
		$this->association->appendJoin($query, self::DEFAULT_ALIAS, self::CONTEXT_ALIAS);
	
		//set condition		
		$args = func_get_args();
		if (empty($args) && $this->hasOption('query.filter'))
			$query->where($this->getFilter(), $this->predicate);
		elseif (isset($condition))
			$query->where(new Filter($args), $this->predicate);
	
		//order by
		if ($this->hasOption('query.order'))
			$query->orderBy($this->getOrderBy());
		
		//build query
		list($sql, $args) = $query->build();
		return $this->mapper->merge($this->clean(['map.type' => $this->buildExpression($this->entityClass)]))->execute($sql, $args);
	}
}