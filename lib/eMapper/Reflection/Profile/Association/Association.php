<?php
namespace eMapper\Reflection\Profile\Association;

use eMapper\Reflection\Profile\PropertyProfile;
use eMapper\Reflection\PropertyAccessor;
use eMapper\Reflection\Profile\ClassProfile;
use eMapper\Manager;
use eMapper\AssociationManager;
use eMapper\Query\Attr;
use Omocha\Annotation;
use Omocha\AnnotationBag;
use Omocha\Filter;

/**
 * The Association class encapsulates common logic between the various types of entity associations.
 * @author emaphp
 */
abstract class Association extends PropertyProfile {
	use PropertyAccessor;

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
	 * Associated entity foreign key
	 * @var string
	 */
	protected $foreignKey;
	
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
	
	/**
	 * Cascade option
	 * @var boolean
	 */
	protected $cascade;
	
	public function __construct($type, $name, AnnotationBag $annotations, \ReflectionProperty $reflectionProperty) {
		//get entity class
		$entity = $annotations->get($type)->getValue();
		
		if (empty($entity) || $entity === true) {
			throw new \RuntimeException(sprintf("Association %s in class %s must define a valid entity class", $name, $reflectionProperty->getDeclaringClass()->getName()));
		}
		
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
		$this->profile = $entity;
		$this->parent = $reflectionProperty->getDeclaringClass()->getName();
		$this->reflectionProperty = $reflectionProperty;
		$this->reflectionProperty->setAccessible(true);
		
		//parse additional configuration
		$this->parseConfig($annotations);
	}

	/**
	 * Parses additional configuration values for this association
	 * @param AnnotationBag $annotations
	 */
	protected function parseConfig(AnnotationBag $annotations) {
		//get additional configuration
		$this->readOnly = $annotations->has('ReadOnly');
		$this->attribute = $annotations->has('Attr') ? $annotations->get('Attr') : null;
		$this->joinWith = $annotations->has('JoinWith') ? $annotations->get('JoinWith') : null;
		$this->foreignKey = $annotations->has('ForeignKey') ? $annotations->get('ForeignKey')->getValue() : null;
		$this->lazy = $annotations->has('Lazy');
		$this->cascade = $annotations->has('Cascade') ? (bool) $annotations->get('Cascade')->getValue() : false;
		
		//parse options
		$this->index = $annotations->has('Index') ? $annotations->get('Index')->getValue() : null;
		
		$order = $annotations->find('OrderBy', Filter::HAS_ARGUMENT);
		
		foreach ($order as $option)
			$this->order[$option->getArgument()] = $option->getValue();
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
	
	public function isCascade() {
		return $this->cascade;
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
		if ($condition === false) return null;

		//build association manager
		$manager = new AssociationManager($mapper, $this, $condition);
		
		//apply indexation
		if (!empty($this->index) && is_string($this->index))
			$manager = $manager->index(Attr::__callstatic($this->index));
		
		//apply order
		if (!empty($this->order)) {
			$order = [];
			
			foreach ($this->order as $key => $value) {
				if (is_bool($value) || (strtolower($value) != 'asc' && strtolower($value) != 'desc'))
					$type = null;
				else
					$type = $value;
				
				$order[] = Attr::__callstatic($key, [$type]);
			}
			
			$manager = $manager->merge(['query.order' => $order]);
		}
		
		if ($this->lazy) return $manager;
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
	 * @param string $joinType
	 */
	public abstract function buildJoin($alias, $mainAlias, $joinType);
	
	/**
	 * Builds the SQL predicate that determines the join condition
	 * @param object $entity
	 */
	public abstract function buildCondition($entity);
	
	/**
	 * Stores associated data
	 * @param \eMapper\Mapper $mapper
	 * @param mixed $parent
	 * @param mixed $value
	 * @param int $depth
	 */
	public abstract function save($mapper, $parent, $value, $depth);
	
	/**
	 * Deletes associated data
	 * @param \eMapper\Mapper $mapper
	 * @param int $foreignKey
	 */
	public abstract function delete($mapper, $foreignKey);
}
?>