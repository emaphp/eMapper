<?php
namespace Acme\Association;

/**
 * @Entity categories
 */
class Category {
	/**
	 * @Id
	 * @Column category_id
	 */
	private $id;
	
	/**
	 * @Column parent_id
	 */
	private $parentId;
	
	/**
	 * @Type string
	 */
	private $name;
	
	/**
	 * @ManyToOne Category
	 * @Attr(parentId)
	 */
	private $parent;
	
	/**
	 * @OneToMany Category
	 * @Attr parentId
	 */
	private $subcategories;
	
	public function getId() {
		return $this->id;
	}
	
	public function setParentId($parentId) {
		$this->parentId = $parentId;
	}
	
	public function getParentId() {
		return $this->parentId;
	}
	
	public function setName($name) {
		$this->name = $name;
	}
	
	public function getName() {
		return $this->name;
	}
	
	public function setParent($parent) {
		$this->parent = $parent;
	}
	
	public function getParent() {
		return $this->parent;
	}
	
	public function setSubcategories($subcategories) {
		$this->subcategories = $subcategories;
	}
	
	public function getSubcategories() {
		return $this->subcategories;
	}
}
?>