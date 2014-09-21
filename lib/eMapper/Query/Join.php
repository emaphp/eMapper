<?php
namespace eMapper\Query;

use eMapper\Reflection\Profile\Association\Association;

/**
 * The Join class is an abstraction for the required joins that need to be done for a given SELECT query.
 * @author emaphp
 */
class Join {
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
	
	public function toSQL($defaultAlias) {
		$parentAlias = is_null($this->parent) ? $defaultAlias : $this->parent->getAlias();
		return $this->association->buildJoin($this->alias, $parentAlias);
	}
}
?>