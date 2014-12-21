<?php
namespace eMapper\Query;

use eMapper\ORM\Association\Association;
use eMapper\Reflection\ClassProfile;

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
	 * Association route
	 * @var array
	 */
	protected $path;
	
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
	
	public function __construct(Association $association, ClassProfile $profile, $path, $alias, $parent) {
		$this->association = $association;
		$this->profile = $profile;
		$this->path = $path;
		$this->alias = $alias;
		$this->parent = $parent;
	}
	
	public function getAssociation() {
		return $this->association;
	}
	
	public function getProfile() {
		return $this->profile;
	}
	
	public function getPath() {
		return $this->path;
	}
	
	public function getAlias() {
		return $this->alias;
	}
	
	public function getParent() {
		return $this->parent;
	}
	
	public function getName() {
		if (empty($this->path))
			return null;
		return implode('__', $this->path);
	}
}