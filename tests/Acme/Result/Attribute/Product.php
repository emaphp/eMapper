<?php
namespace Acme\Result\Attribute;

/**
 * @meta.parser emapper\emapper
 * @map.entity
 * @author emaphp
 */
class Product {
	/**
	 * @map.column product_id
	 * @var integer
	 */
	public $id;
	
	/**
	 * @map.type string
	 */
	public $category;
	
	/**
	 * @map.type color
	 */
	public $color;
	
	/**
	 * @map.procedure Sales_FindLastByProductId
	 * @map.arg #id
	 * @map.type obj:Acme\Result\Attribute\Sale
	 */
	public $lastSale;
	
	/**
	 * @map.procedure Products_FindBestByCategory
	 * @map.arg #category
	 * @map.type obj:Acme\Result\Attribute\Product
	 */
	public $bestInCategory;
	
	/**
	 * @map.procedure Products_FindAvgPriceByCategory
	 * @map.arg #category
	 * @map.type float
	 */
	public $avgPrice;
}
?>