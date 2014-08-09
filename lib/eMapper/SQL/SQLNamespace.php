<?php
namespace eMapper\SQL;

use eMapper\SQL\Aggregate\SQLNamespaceAggregate;

/**
 * The SQLNamespace class represents a container of statements and other namespaces.
 * @author emaphp
 */
class SQLNamespace {
	use SQLNamespaceAggregate;
	
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
	protected $id;
	
	public function __construct($id) {
		$this->validateNamespaceId($id);
		$this->id = $id;
	}
	
	public function getId() {
		return $this->id;
	}
}
?>