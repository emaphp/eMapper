<?php
namespace eMapper\Reflection\Profile\Association;

use eMapper\Reflection\Profile\PropertyProfile;
use eMapper\Annotations\Annotation;
use eMapper\Manager;
use eMapper\Annotations\AnnotationsBag;
use eMapper\AssociationManager;
use eMapper\Annotations\Filter;
use eMapper\Query\Attr;

/**
 * The Association class encapsulates common logic between the various types of entity associations.
 * @author emaphp
 */
abstract class Association extends PropertyProfile {
	const DEFAULT_ALIAS = '_t';
	const CONTEXT_ALIAS = '_c';
	
	/**
	 * Referred entity profile
	 * @var string
	 */
	protected $profile;
	
	/**
	 * Declaring class profile
	 * @var string
	 */
	protected $parent;
	
	/**
	 * Join table configuration
	 * @var Annotation
	 */
	protected $joinWith;
	
	/**
	 * Determines whether the association is lazy or not
	 * @var boolean
	 */
	protected $lazy;
	
	/**
	 * Index attribute
	 * @var string
	 */
	protected $index;
	
	/**
	 * Order criteria
	 * @var array
	 */
	protected $order = [];
		
	public function __construct($entity, $name, AnnotationsBag $annotations, \ReflectionProperty $reflectionProperty) {
		try {
			$reflectionClass = new \ReflectionClass($entity);
		}
		catch (\ReflectionException $re) {
			//try using
			$currentNamespace = $reflectionProperty->getDeclaringClass()->getNamespaceName();
			$entity = $currentNamespace . '\\' . $entity;
			$reflectionClass = new \ReflectionClass($entity);
		}
		
		$this->name = $name;
		
		//get entities profiles
		$this->profile = $entity;
		$this->parent = $reflectionProperty->getDeclaringClass()->getName();
		
		//get additional configuration
		$this->column = $annotations->has('Column') ? $annotations->get('Column') : null;
		$this->attribute = $annotations->has('Attr') ? $annotations->get('Attr') : null;
		$this->joinWith = $annotations->has('JoinWith') ? $annotations->get('JoinWith') : null;
		$this->lazy = $annotations->has('Lazy');
		
		//parse options
		$this->index = $annotations->has('Index') ? $annotations->get('Index')->getValue() : null;
		
		$order = $annotations->find('OrderBy', Filter::HAS_ARGUMENT);
		
		foreach ($order as $option) {
			$this->order[$option->getArgument()] = $option->getValue();
		}
		
		$this->reflectionProperty = $reflectionProperty;
		$this->reflectionProperty->setAccessible(true);
	}
	
	public function getProfile() {
		return $this->profile;
	}
	
	public function getParent() {
		return $this->parent;
	}
	
	public function getForeignKey() {
		return $this->foreignKey;
	}
	
	public function getJoinWith() {
		return $this->joinWith;
	}
	
	public function isLazy() {
		return $this->lazy;
	}
	
	/**
	 * Evaluates current association
	 * @param mixed $entity
	 * @param Mapper $mapper
	 * @return \eMapper\Manager
	 */
	public function evaluate($entity, $mapper) {
		//build join condition
		$condition = $this->buildCondition($entity);
		
		if ($condition === false) {
			return null;
		}
		
		//build association manager
		$manager = new AssociationManager($mapper, $this, $condition);
		
		//apply indexation
		if (!empty($this->index) && is_string($this->index)) {
			$manager = $manager->index(Attr::__callstatic($this->index));
		}
		
		//apply order
		if (!empty($this->order)) {
			$order = [];
			
			foreach ($this->order as $key => $value) {
				if (is_boolean($value) || (strtolower($value) != 'asc' && strtolower($value) != 'desc')) {
					$type = null;
				}
				else {
					$type = $value;
				}
				
				$order[] = Attr::__callstatic($key, [$type]);
			}
			
			$manager = $manager->merge(['query.order' => $order]);
		}
		
		if ($this->lazy) {
			return $manager;
		}
		
		return $this->fetchValue($manager);
	}
	
	/**
	 * Fetchs the requested value according to association type
	 * @param Manager $manager
	 */
	public abstract function fetchValue(Manager $manager);
	
	/**
	 * Builds the SQL for joining tables
	 * @param string $alias
	 * @param string $mainAlias
	 */
	public abstract function buildJoin($alias, $mainAlias);
	
	/**
	 * Builds the SQL predicate that determines the join condition
	 * @param object $entity
	 */
	public abstract function buildCondition($entity);
}
?>