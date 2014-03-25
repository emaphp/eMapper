<?php
namespace Acme\Result;

/**
 * @parser emapper\emapper
 * @author emaphp
 */
class GenericProductResultMap {
	/**
	 * @column product_id
	 * @setter setId
	 * @var int
	 */
	protected $id;
	
	/**
	 * @setter setDescription
	 * @var string
	 */
	protected $description;
	
	/**
	 * @setter setCode
	 * @column product_code
	 * @var string
	 */
	protected $code;
	
	/**
	 * @setter setPrice
	 * @var float
	 */
	protected $price;
	
	/**
	 * @setter setCategory
	 * @var string
	 */
	protected $category;
	
	/**
	 * @setter setColor
	 * @var Acme\RGBColor
	 */
	protected $color;
}
?>