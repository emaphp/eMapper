<?php
namespace eMapper\ORM\Association;

use eMapper\Reflection\ClassProperty;
use eMapper\Reflection\PropertyAccessor;
use eMapper\ORM\AssociationManager;
use eMapper\Fluent\Query\AbstractQuery;
use eMapper\Query\Attr;
use eMapper\Mapper;
use Omocha\AnnotationBag;
use Omocha\Filter;

/**
 * The Association class encapsulates common logic between the various types of entity associations.
 * @author emaphp
 */
abstract class Association extends ClassProperty {
	use PropertyAccessor;
	
	/**
	 * Associated entity class
	 * @var string
	 */
	protected $entityClass;
	
	/**
	 * Parent class
	 * @var string
	 */
	protected $parentClass;
	
	/**
	 * Related attribute
	 * @var \Omocha\Annotation
	 */
	protected $attribute;
	
	/**
	 * Indicates if association is lazy
	 * @var boolean
	 */
	protected $lazy;
	
	/**
	 * Indicates if associated entities are removed manually
	 * @var boolean
	 */
	protected $cascade;
	
	/**
	 * Join table
	 * @var \Omocha\Annotation
	 */
	protected $join;
	
	/**
	 * Index attribute
	 * @var string
	 */
	protected $index;
	
	/**
	 * Order attribute
	 * @var array
	 */
	protected $order;
	
	/**
	 * Order attributes
	 * @var \Omocha\Annotation
	 */
	protected $cache;
	
	public function __construct($type, $propertyName, \ReflectionProperty $reflectionProperty, AnnotationBag $propertyAnnotations) {
		parent::__construct($propertyName, $reflectionProperty, $propertyAnnotations);
		
		//get entity name
		$entity = $propertyAnnotations->get($type)->getValue();
		
		try {
			new \ReflectionClass($entity);
		}
		catch (\ReflectionException $re) {
			//try using
			$currentNamespace = $reflectionProperty->getDeclaringClass()->getNamespaceName();
			$entity = $currentNamespace . '\\' . $entity;
		}
		
		$this->entityClass = $entity;
		$this->parentClass = $reflectionProperty->getDeclaringClass()->getName();
		
		//parse additional configuration
		$this->parseConfig($propertyAnnotations);
	}
	
	/**
	 * Returns a SQLPredicate instance with the required join condition
	 * @param array | object $entity
	 * @return \eMapper\SQL\Predicate\SQLPredicate
	 */
	abstract function getJoinPredicate($entity);
	
	/**
	 * Appends the required joins to a fluent query instance
	 * @param \eMapper\Fluent\Query\AbstractQuery $query
	 * @param string $mainAlias
	 * @param string $contextAlias
	 */
	abstract function appendJoin(AbstractQuery &$query, $mainAlias, $contextAlias);
	
	/**
	 * Fetchs associated data
	 * @param \eMapper\ORM\AssociationManager $manager
	 * @return mixed
	 */
	abstract function fetchValue(AssociationManager $manager);
	
	/**
	 * Saves associated data
	 * @param \eMapper\Mapper $mapper
	 * @param array | object $parent
	 * @param mixed $value
	 * @param int $depth
	 */
	abstract function save(Mapper $mapper, $parent, $value, $depth);
	
	/**
	 * Deletes associated data
	 * @param \eMapper\Mapper $mapper
	 * @param mixed $foreignKey
	 */
	abstract function delete(Mapper $mapper, $foreignKey);
	
	/**
	 * Parses configuration
	 * @param \Omocha\AnnotationBag $propertyAnnotations
	 */
	protected function parseConfig(AnnotationBag $propertyAnnotations) {
		$this->attribute = $propertyAnnotations->has('Attr') ? $propertyAnnotations->get('Attr') : null;
		$this->lazy = $propertyAnnotations->has('Lazy');
		$this->cascade = $propertyAnnotations->has('Cascade');
		$this->join = $propertyAnnotations->has('Join') ? $propertyAnnotations->get('Join') : null;
		
		//parse options
		$this->index = $propertyAnnotations->has('Index') ? $propertyAnnotations->get('Index')->getValue() : null;
		$order = $propertyAnnotations->find('OrderBy', Filter::HAS_ARGUMENT);
		
		if (!empty($order)) {
			$this->order = [];
				
			foreach ($order as $option)
				$this->order[] = $option->getValue();
		}
		
		$this->cache = $propertyAnnotations->has('Cache') ? $propertyAnnotations->get('Cache') : null;
	}
	
	/**
	 * Evaluates association
	 * @param array | object $entity
	 * @param \eMapper\Mapper $mapper
	 * @return mixed
	 */
	public function evaluate($entity, Mapper $mapper) {
		//build sql predicate
		$predicate = $this->getJoinPredicate($entity);
		if (is_null($predicate))
			return null;
		
		//build association manager
		$manager = new AssociationManager($mapper, $this, $predicate);
		
		//fetch data
		if ($this->lazy)
			return $manager;
		
		return $this->fetchValue($manager);
	}
	
	public function getEntityClass() {
		return $this->entityClass;
	}
	
	public function getParentClass() {
		return $this->parentClass;
	}
	
	public function getAttribute() {
		return $this->attribute;
	}
	
	public function isLazy() {
		return $this->lazy;
	}
	
	public function isCascade() {
		return $this->cascade;
	}
	
	public function getJoin() {
		return $this->join;
	}
	
	public function getIndex() {
		return $this->index;
	}
	
	public function getOrder() {
		return $this->order;
	}
	
	public function getCache() {
		return $this->cache;
	}
}