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
	 * @Parameter(id)
	 * @Type obj:Acme\Result\Attribute\Sale
	 * @AsTable true
	 * @Escape false
	 */
	public $lastSale;
	
	/**
	 * @Procedure Products_FindBestByCategory
	 * @Parameter(category)
	 * @Type obj:Acme\Result\Attribute\Product
	 * @AsTable true
	 * @Escape false
	 */
	public $bestInCategory;
	
	/**
	 * @Procedure Products_FindAvgPriceByCategory
	 * @Parameter(category)
	 * @Type float
	 * @Escape false
	 * @Scalar
	 */
	public $avgPrice;
}
?>