<?php
namespace Acme\Statement;

/**
 * @ResultMap
 */
class EndsWithResultMap {
	/**
	 * @Column sale_id
	 */
	public $saleId;
	
	/**
	 * @Statement Product.codeEndsWith
	 * @Param '3'
	 */
	public $endsWith;
	
	/**
	 * @Statement Product.categoryNotEndsWith
	 * @Param 'es'
	 */
	public $notEndsWith;
	
	/**
	 * @Statement Product.categoryIEndsWith
	 * @Param 'RE'
	 */
	public $iendsWith;
	
	/**
	 * @Statement Product.descriptionNotIEndsWith
	 * @Param 'NE'
	 */
	public $notIEndsWith;
}