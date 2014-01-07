<?php
namespace eMapper\Statement;

use eMapper\Statement\Aggregate\StatementNamespaceAggregate;

class StatementNamespace {
	use StatementNamespaceAggregate;
	
	/**
	 * Namespace ID validation regex
	 * @var string
	 */
	const NAMESPACE_ID_REGEX = '@[\w]+@';
	
	/**
	 * Inner namespace regex
	 * @var string
	 */
	const INNER_NAMESPACE_REGEX = '@(\\w+)\.(.+)@';
	
	/**
	 * Namespace ID
	 * @var string
	 */
	public $id;
	
	public function __construct($id) {
		$this->validateNamespaceId($id);
		$this->id = $id;
	}
	
	public static function create($id) {
		return new StatementNamespace($id);
	}
}
?>