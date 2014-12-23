<?php
namespace Acme\Result\Attribute;

/**
 * @Entity products
 */
class Product {
	/**
	 * @Id
	 * @Column product_id
	 * @Type integer
	 */
	public $id;
	
	/**
	 * @Type string
	 */
	public $category;
	
	/**
	 * @Type color
	 */
	public $color;
	
	/**
	 * @Procedure Sales_FindLastByProductId
	 * @Param(id)
	 * @Type obj:Acme\Result\Attribute\Sale
	 * @ReturnSet
	 */
	public $lastSale;
	
	/**
	 * @Procedure Products_FindBestByCategory
	 * @Param(category)
	 * @Type obj:Acme\Result\Attribute\Product
	 * @ReturnSet
	 */
	public $bestInCategory;
	
	/**
	 * @Procedure Products_FindAvgPriceByCategory
	 * @Param(category)
	 * @Type float
	 * @Cacheable
	 */
	public $avgPrice;
}
?>
