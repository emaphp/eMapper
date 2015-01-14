<?php
namespace Acme\Statement;

/**
 * @ResultMap
 */
class MatchesResultMap {
	/**
	 * @Column sale_id
	 */
	public $saleId;
	
	/**
	 * @Statement Product.codeMatches
	 * @Param ^IND0+
	 */
	public $matches;
	
	/**
	 * @Statement Product.codeNotMatches
	 * @Param ^IND0+
	 */
	public $notMatches;
	
	/**
	 * @Statement Product.codeIMatches
	 * @Param ^phn0+
	 */
	public $imatches;
	
	/**
	 * @Statement Product.codeNotIMatches
	 * @Param ^phn0+
	 */
	public $notIMatches;
}