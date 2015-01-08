<?php
namespace Acme\Statement;

/**
 * @ResultMap
 */
class IsNullResultMap {
	/**
	 * @Column sale_id
	 */
	public $saleId;
	
	/**
	 * @Statement Product.colorIsNull
	 */
	public $isNull;
	
	/**
	 * @Statement Product.colorIsNotNull
	 */
	public $isNotNull;
}