<?php
namespace Acme\Statement;

/**
 * @ResultMap
 */
class StartsWithResultMap {
	/**
	 * @Column sale_id
	 */
	public $saleId;
	
	/**
	 * @Statement Product.codeStartsWith
	 * @Param IND
	 */
	public $startsWith;
	
	/**
	 * @Statement Product.codeNotStartsWith
	 * @Param IND
	 */
	public $notStartsWith;
	
	/**
	 * @Statement Product.codeIStartsWith
	 * @Param SOFT
	 */
	public $istartsWith;
	
	/**
	 * @Statement Product.codeNotIStartsWith
	 * @Param SOFT
	 */
	public $notIStartsWith;
}