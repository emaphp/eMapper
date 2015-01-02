<?php
namespace eMapper\Query;

use eMapper\ORM\Association\Association;
use eMapper\Reflection\ClassProfile;

/**
 * The Join class abstracts a join clause required by an ORM operation.
 * @author emaphp
 */
class Join {
	/**
	 * Association instance
	 * @var \eMapper\ORM\Association\Association
	 */
	protected $association;
	
	/**
	 * Entity profile
	 * @var \eMapper\Reflection\ClassProfile
	 */
	protected $profile;
	
	/**
	 * Join table alias
	 * @var string
	 */
	protected $alias;
	
	/**
	 * Parent association name
	 * @var string
	 */
	protected $parent;
	
	public function __construct(Association $association, ClassProfile $profile, $alias, $parent) {
		$this->association = $association;
		$this->profile = $profile;
		$this->alias = $alias;
		$this->parent = $parent;
	}
	
	public function getAssociation() {
		return $this->association;
	}
	
	public function getProfile() {
		return $this->profile;
	}
	
	public function getAlias() {
		return $this->alias;
	}
	
	public function getParent() {
		return $this->parent;
	}
}