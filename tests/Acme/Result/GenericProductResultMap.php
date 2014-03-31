<?php
namespace Acme\Result;

/**
 * @meta.parser emapper\emapper
 * @author emaphp
 */
class GenericProductResultMap {
	/**
	 * @map.column product_id
	 * @var int
	 */
	protected $id;
	
	/**
	 * @var string
	 */
	protected $description;
	
	/**
	 * @map.column product_code
	 * @var string
	 */
	protected $code;
	
	/**
	 * @var float
	 */
	protected $price;
	
	/**
	 * @var string
	 */
	protected $category;
	
	/**
	 * @var Acme\RGBColor
	 */
	protected $color;
}
?>