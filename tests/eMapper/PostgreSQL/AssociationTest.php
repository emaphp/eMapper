<?php
namespace eMapper\PostgreSQL;

use eMapper\AbstractAssociationTest;

/**
 * 
 * @author emaphp
 * @group association
 * @group postgre
 */
class AssociationTest extends AbstractAssociationTest {
	use PostgreSQLConfig;
	
	public function setUp() {
		$this->mapper = $this->getMapper();
	}
}
?>