<?php
namespace eMapper\SQL\Builder;

use eMapper\Reflection\Profile\Association\Association;

/**
 * The AssociationJoin class is an abstraction for the required joins that need to be done for a given SELECT query.
 * @author emaphp
 */
class AssociationJoin {
	const INNER = 'INNER';
	const LEFT = 'LEFT OUTER';
	
	/**
	 * Join name
	 * @var string
	 */
	protected $name;
	
	/**
	 * Association value
	 * @var Association
	 */
	protected $association;
	
	/**
	 * Parent join
	 * @var Join
	 */
	protected $parent;
	
	/**
	 * Parent join name
	 * @var string
	 */
	protected $parentName;
	
	/**
	 * Join alias
	 * @var string
	 */
	protected $alias;
	
	/**
	 * Join type
	 * @var string
	 */
	protected $type = self::INNER;
	
	public function __construct($name, $association, $parentName) {
		$this->name = $name;
		$this->association = $association;
		$this->parentName = $parentName;
	}
	
	public function getName() {
		return $this->name;
	}
	
	public function getAssociation() {
		return $this->association;
	}
	
	public function getParentName() {
		return $this->parentName;
	}
	
	public function setAlias($alias) {
		$this->alias = $alias;
	}
	
	public function getAlias() {
		return $this->alias;
	}
	
	public function setParent($parent) {
		$this->parent = $parent;
	}
	
	public function getParent() {
		return $this->parent;
	}
	
	public function setType($type) {
		$this->type = $type;
	}
	
	public function getType() {
		return $this->type;
	}
	
	public function toSQL($defaultAlias) {
		$parentAlias = is_null($this->parent) ? $defaultAlias : $this->parent->getAlias();
		return $this->association->buildJoin($this->alias, $parentAlias, $this->type);
	}
}
?>