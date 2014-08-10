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
	 * @Option(proc.as_table) true
	 * @Option(proc.wrap) false
	 */
	public $lastSale;
	
	/**
	 * @Procedure Products_FindBestByCategory
	 * @Parameter(category)
	 * @Type obj:Acme\Result\Attribute\Product
	 * @Option(proc.as_table) true
	 * @Option(proc.wrap) false
	 */
	public $bestInCategory;
	
	/**
	 * @Procedure Products_FindAvgPriceByCategory
	 * @Parameter(category)
	 * @Type float
	 * @Option(proc.wrap) false
	 * @Scalar
	 */
	public $avgPrice;
}
?>