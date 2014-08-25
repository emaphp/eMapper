<?php
namespace eMapper\PostgreSQL;

use eMapper\AbstractAssociationQueryTest;

/**
 * 
 * @author emaphp
 * @group postgre
 * @group association
 * @group query
 */
class AssociationQueryTest extends AbstractAssociationQueryTest {
	use PostgreSQLConfig;
	
	public function setUp() {
		$this->mapper = $this->getMapper();
	}
}
?>