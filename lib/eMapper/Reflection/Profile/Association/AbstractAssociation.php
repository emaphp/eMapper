<?php
namespace eMapper\Reflection\Profile\Association;

use eMapper\Reflection\Profile\PropertyProfile;
use eMapper\Reflection\Profiler;
use eMapper\Annotations\Annotation;
use eMapper\Reflection\Profile\ClassProfile;
use eMapper\Manager;

abstract class AbstractAssociation extends PropertyProfile {
	/**
	 * Referred entity profile
	 * @var ClassProfile
	 */
	protected $profile;
	
	/**
	 * Declaring class profile
	 * @var ClassProfile
	 */
	protected $from;
	
	/**
	 * Foreign key
	 * @var string
	 */
	protected $foreignKey;
	
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
		//check if the specified class is a valid entity
		try {
			$profile = Profiler::getClassProfile($entity);
		}
		catch (\ReflectionException $re) {
			//try using
			$currentNamespace = $reflectionProperty->getDeclaringClass()->getNamespaceName();
			$entity = $currentNamespace . '\\' . $entity;
			$profile = Profiler::getClassProfile($entity);
		}
		
		if (!$profile->isEntity()) {
			throw new \UnexpectedValueException(sprintf("Class %s is not a valid entity", $entity));
		}
		
		$this->name = $name;
		
		//get entities profiles
		$this->profile = $profile;
		$this->from = Profiler::getClassProfile($reflectionProperty->getDeclaringClass()->getName());
		
		//get additional configuration
		$this->column = $annotations->has('Column') ? $annotations->get('Column')->getValue() : null;
		$this->foreignKey = $annotations->has('ForeignKey') ? $annotations->get('ForeignKey')->getValue() : null;
		$this->joinWith = $annotations->has('JoinWith') ? $annotations->get('JoinWith') : null;
		$this->reversedBy = $annotations->has('ReversedBy') ? $annotations->get('ReversedBy')->getValue() : null;
		$this->lazy = $annotations->has('Lazy');
		
		$this->reflectionProperty = $reflectionProperty;
		$this->reflectionProperty->setAccessible(true);
	}
	
	public function getProfile() {
		return $this->profile;
	}
	
	public function getReversedBy() {
		return $this->reversedBy;
	}
	
	public function isLazy() {
		return $this->lazy;
	}
	
	/**
	 * Evaluates current association
	 * @param mixed $row
	 * @param Mapper $mapper
	 * @return \eMapper\Manager
	 */
	public function evaluate($mapper) {
		$manager = new Manager($mapper, $this->profile, $this);
		
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
}
?>