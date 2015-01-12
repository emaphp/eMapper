<?php
namespace Acme\Storage;

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
	 * @Nullable
	 */
	public $parentId;
	
	/**
	 * @Type string
	 */
	public $name;
	
	/**
	 * @ManyToOne Category
	 * @Attr(parentId)
	 */
	public $parent;
	
	/**
	 * @OneToMany Category
	 * @Attr parentId
	 * @Index name
	 * @Cascade
	 */
	public $subcategories;
}