<?php
namespace Acme\Result\Attribute;

/**
 * @entity
 * @author emaphp
 */
class Product {
	/**
	 * @column product_id
	 * @var integer
	 */
	public $id;
	
	/**
	 * @type string
	 */
	public $category;
	
	/**
	 * @type color
	 */
	public $color;
	
	/**
	 * @procedure Sales_FindLastByProductId
	 * @arg #id
	 * @type obj:Acme\Result\Attribute\Sale
	 */
	public $lastSale;
	
	/**
	 * @procedure Products_FindBestByCategory
	 * @arg #category
	 * @type obj:Acme\Result\Attribute\Product
	 */
	public $bestInCategory;
	
	/**
	 * @procedure Products_FindAvgPriceByCategory
	 * @arg #category
	 * @type float
	 */
	public $avgPrice;
}
?>