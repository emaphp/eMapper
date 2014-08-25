<?php
namespace eMapper\SQLite;

use eMapper\AbstractAssociationQueryTest;

/**
 * 
 * @author emaphp
 * @group sqlite
 * @group association
 * @group query
 */
class AssociationQueryTest extends AbstractAssociationQueryTest {
	use SQLiteConfig;
	
	public function setUp() {
		$this->mapper = $this->getMapper();
	}
}
?>