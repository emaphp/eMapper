<?php
namespace eMapper;

use eMapper\Reflection\Profile\Association\AbstractAssociation;
use eMapper\Query\Predicate\SQLPredicate;
use eMapper\Reflection\Profiler;
use eMapper\Query\Builder\SelectQueryBuilder;
use eMapper\Query\Aggregate\SQLFunction;

class AssociationManager extends Manager {
	/**
	 * Association context
	 * @var AbstractAssociation
	 */
	protected $association;
	
	/**
	 * Join condition
	 * @var SQLPredicate
	 */
	protected $condition;
	
	public function __construct(Mapper $mapper, AbstractAssociation $association, SQLPredicate $condition) {
		$this->mapper = $mapper;
		$this->association = $association;
		$this->condition = $condition;
		
		//get entity profile
		$this->entity = Profiler::getClassProfile($association->getProfile());
		
		//default mapping expression
		$this->expression = $this->buildExpression($this->entity);
	}
	
	/**
	 * Obtains a list of entities by the given condition
	 * @param SQLPredicate $condition
	 * @return mixed
	 */
	public function find(SQLPredicate $condition = null) {
		//connect to database
		$this->mapper->connect();
	
		//build query
		$query = new SelectQueryBuilder($this->entity);
		$query->setCondition($condition);
		$query->setJoin($this->association, $this->condition);
	
		list($query, $args) = $query->build($this->mapper->getDriver(), $this->config);
	
		//run query
		$options = $this->clean_options(['map.type' => $this->getListMappingExpression()]);
		return $this->mapper->merge($options)->query($query, $args);
	}
	
	/**
	 * Returns the first result from a query
	 * @param SQLPredicate $condition
	 * @return NULL|object
	 */
	public function get(SQLPredicate $condition = null) {
		//connect to database
		$this->mapper->connect();
	
		//build query
		$query = new SelectQueryBuilder($this->entity);
		$query->setCondition($condition);
		$query->setJoin($this->association, $this->condition);
	
		list($query, $args) = $query->build($this->mapper->getDriver(), $this->config);
	
		//run query
		$options = $this->clean_options(['map.type' => $this->expression]);
		return $this->mapper->merge($options)->query($query, $args);
	}
	
	protected function sqlFunction(SQLFunction $function, $type) {
		//connect to database
		$this->mapper->connect();
	
		//build query
		$query = new SelectQueryBuilder($this->entity);
		$query->setFunction($function);
		$query->setJoin($this->association, $this->condition);
		list($query, $args) = $query->build($this->mapper->getDriver(), $this->config);
	
		//run query
		$options = $this->clean_options(['map.type' => $type]);
		return $this->mapper->merge($options)->query($query, $args);
	}
}
?>