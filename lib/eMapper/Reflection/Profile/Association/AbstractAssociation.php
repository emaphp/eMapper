<?php
namespace eMapper\Reflection\Profile\Association;

use eMapper\Reflection\Profile\PropertyProfile;
use eMapper\Annotations\Annotation;
use eMapper\Manager;
use eMapper\Annotations\AnnotationsBag;
use eMapper\AssociationManager;

/**
 * The AbstractAssociation class encapsulates common logic between the various types of entity associations.
 * @author emaphp
 */
abstract class AbstractAssociation extends PropertyProfile {
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
	 * Property reversing this association
	 * @var string
	 */
	protected $reversedBy;
	
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
		$this->reversedBy = $annotations->has('ReversedBy') ? $annotations->get('ReversedBy')->getValue() : null;
		$this->lazy = $annotations->has('Lazy');
		
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
	
	public function getReversedBy() {
		return $this->reversedBy;
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
		
		//build association manager
		$manager = new AssociationManager($mapper, $this, $condition);
		
		if ($this->lazy) {
			return $manager;
		}
		
		return $this->fetchValue($manager);
	}
	
	/**
	 * Fetchs the requested value according to association type
	 * @param Manager $manager
	 */
	protected abstract function fetchValue(Manager $manager);
	
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