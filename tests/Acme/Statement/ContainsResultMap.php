<?php
namespace Acme\Statement;

/**
 * @ResultMap
 */
class ContainsResultMap {
	/**
	 * @Column sale_id
	 */
	public $saleId;
	
	/**
	 * @Statement Product.descriptionContains
	 * @Param 'Android'
	 */
	public $contains;
	
	/**
	 * @Statement Product.codeNotContains
	 * @Param 'IND'
	 */
	public $notContains;
	
	/**
	 * @Statement Product.descriptionIContains
	 * @Param 're'
	 */
	public $icontains;
	
	/**
	 * @Statement Product.descriptionNotIContains
	 * @Param 're'
	 */
	public $notIContains;
}
