<?php
namespace eMapper;

use eMapper\Reflection\Profile\Association\Association;
use eMapper\Query\Predicate\SQLPredicate;
use eMapper\Reflection\Profiler;
use eMapper\Query\Builder\SelectQueryBuilder;
use eMapper\Query\Aggregate\SQLFunction;

/**
 * An AssociationManager is a class aimed to fetch a result from an existing association
 * between 2 entity classes.
 * @author emaphp
 */
class AssociationManager extends Manager {
	/**
	 * Association context
	 * @var Association
	 */
	protected $association;
	
	/**
	 * Join condition
	 * @var SQLPredicate
	 */
	protected $condition;
	
	public function __construct(Mapper $mapper, Association $association, SQLPredicate $condition) {
		$this->mapper = $mapper;
		$this->association = $association;
		$this->condition = $condition;
		
		//get entity profile
		$this->entity = Profiler::getClassProfile($association->getProfile());
		
		//default mapping expression
		$this->expression = $this->buildExpression($this->entity);
	}
	
	/**
	 * Fetchs the associated value(s)
	 */
	public function fetch() {
		return $this->association->fetchValue($this);
	}
	
	public function find(SQLPredicate $condition = null) {
		//connect to database
		$this->mapper->connect();
	
		//build query
		$query = new SelectQueryBuilder($this->entity);
		$query->setCondition($condition);
		$query->setContext($this->association, $this->condition);
	
		list($query, $args) = $query->build($this->mapper->getDriver(), $this->config);
		
		//run query
		return $this->mapper->merge($this->cleanConfig(['map.type' => $this->getListMappingExpression()]))->query($query, $args);
	}
	
	public function get(SQLPredicate $condition = null) {
		//connect to database
		$this->mapper->connect();
	
		//build query
		$query = new SelectQueryBuilder($this->entity);
		$query->setCondition($condition);
		$query->setContext($this->association, $this->condition);
	
		list($query, $args) = $query->build($this->mapper->getDriver(), $this->config);
	
		//run query
		return $this->mapper->merge($this->cleanConfig(['map.type' => $this->expression]))->query($query, $args);
	}
	
	protected function sqlFunction(SQLFunction $function, $type) {
		//connect to database
		$this->mapper->connect();
	
		//build query
		$query = new SelectQueryBuilder($this->entity);
		$query->setFunction($function);
		$query->setContext($this->association, $this->condition);
		list($query, $args) = $query->build($this->mapper->getDriver(), $this->config);
	
		//run query
		return $this->mapper->merge($this->cleanConfig(['map.type' => $type]))->query($query, $args);
	}
}
?>