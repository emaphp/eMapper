<?php
namespace Acme\Statement;

/**
 * @Entity categories
 */
class Category {
	/**
	 * @Id
	 * @Type int
	 * @Column category_id
	 */
	public $id;
	
	/**
	 * @Type int
	 * @Column parent_id
	 */
	public $parentId;
	
	/**
	 * @Type string
	 */
	public $name;
	
	/**
	 * @Statement Category.findByPk
	 * @Param(parentId)
	 */
	public $parent;
	
	/**
	 * @Statement Category.findByParentId
	 * @Param(id)
	 */
	public $subcategories;
}