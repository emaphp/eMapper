<?php
namespace Acme\Result;

/**
 * @defaultClass Acme\Generic\GenericProduct
 */
class GenericProductResultMap {
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
	 * @setter setColor
	 * @var Acme\RGBColor
	 */
	protected $color;
}
?>