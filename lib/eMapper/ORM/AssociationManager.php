<?php
namespace eMapper\ORM;

use eMapper\Mapper;
use eMapper\ORM\Association\Association;
use eMapper\SQL\Predicate\SQLPredicate;
use eMapper\Reflection\Profiler;
use eMapper\Query\Schema;
use eMapper\Query\Column;

/**
 * The AssociationManager class is aimed to fetch a result from an existing association between 2 entity classes.
 * @author emaphp
 */
class AssociationManager extends Manager {
	/**
	 * Association context
	 * @var \eMapper\ORM\Association\Association
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
		
		//additional attributes lists
		foreach ($this->entityProfile->getSelectAttributes() as $attr)
			$this->selectColumns[] = new Column($this->entityProfile->getProperty($attr)->getColumn());
		
		$this->preserveInstance = true;
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
		$query = $this->mapper->newQuery($this->entityProfile)->from($this->entityProfile->getEntityTable(), Schema::DEFAULT_ALIAS);
		
		//columns
		$query->select($this->selectColumns);
		
		//append call to required join
		$this->association->appendContextJoin($query, Schema::DEFAULT_ALIAS, Schema::CONTEXT_ALIAS);
		
		//set condition		
		$query->where($this->predicate);
		
		//index
		$index = $this->association->getIndex();
		if (!empty($index))
			$this->setOption('query.index', $index);
		
		//order by
		$order = $this->association->getOrder();
		if (!empty($order)) {
			$this->setOption('query.orderBy', $order);
			$query->orderBy($this->getOrderBy());
		}
		
		//build mapper configuration
		$config = $this->clean(['map.type' => $this->getListMappingExpression()]);
		$cache = $this->association->getCache();
		if (!empty($cache)) {
			$config['cache.key'] = $cache->getValue();
			$config['cache.ttl'] = intval($cache->getArgument());
		}
		
		//build query
		list($sql, $args) = $query->build();
		return $this->mapper->merge($config)->execute($sql, $args);
	}
	
	public function get(SQLPredicate $condition = null) {
		$this->mapper->connect();
	
		//build fluent query
		$query = $this->mapper->newQuery($this->entityProfile)->from($this->entityProfile->getEntityTable(), Schema::DEFAULT_ALIAS);
	
		//columns
		$query->select($this->selectColumns);
		
		//append call to required join
		$this->association->appendContextJoin($query, Schema::DEFAULT_ALIAS, Schema::CONTEXT_ALIAS);
	
		//set condition		
		$query->where($this->predicate);
	
		//order by
		$order = $this->association->getOrder();
		if (!empty($order)) {
			$this->setOption('query.orderBy', $order);
			$query->orderBy($this->getOrderBy());
		}
		
		//build mapper configuration
		$config = $this->clean(['map.type' => $this->buildExpression($this->entityProfile)]);
		$cache = $this->association->getCache();
		if (!empty($cache)) {
			$config['cache.key'] = $cache->getValue();
			$config['cache.ttl'] = intval($cache->getArgument());
		}
		
		//build query
		list($sql, $args) = $query->build();
		return $this->mapper->merge($config)->execute($sql, $args);
	}
}