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
	 * @map.option.proc.as_table true
	 * @map.option.proc.wrap false
	 */
	public $lastSale;
	
	/**
	 * @map.procedure Products_FindBestByCategory
	 * @map.arg #category
	 * @map.type obj:Acme\Result\Attribute\Product
	 * @map.option.proc.as_table true
	 * @map.option.proc.wrap false
	 */
	public $bestInCategory;
	
	/**
	 * @map.procedure Products_FindAvgPriceByCategory
	 * @map.arg #category
	 * @map.type float
	 * @map.option.proc.wrap false
	 */
	public $avgPrice;
}
?>