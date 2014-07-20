<?php
namespace Acme\Result;

/**
 * @ResultMap
 */
class GenericProductResultMap {
	/**
	 * @Id
	 * @Column product_id
	 * @Type integer
	 */
	protected $id;
	
	/**
	 * @Type string
	 */
	protected $description;
	
	/**
	 * @Column product_code
	 * @Type string
	 */
	protected $code;
	
	/**
	 * @Type float
	 */
	protected $price;
	
	/**
	 * @Type string
	 */
	protected $category;
	
	/**
	 * @Type Acme\RGBColor
	 */
	protected $color;
}
?>