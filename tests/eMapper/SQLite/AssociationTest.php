<?php
namespace eMapper\SQLite;

use eMapper\AbstractAssociationTest;

/**
 * 
 * @author emaphp
 * @group association
 * @group sqlite
 */
class AssociationTest extends AbstractAssociationTest {	
	use SQLiteConfig;
	
	public function setUp() {
		$this->mapper = $this->getMapper();
	}
}
?>