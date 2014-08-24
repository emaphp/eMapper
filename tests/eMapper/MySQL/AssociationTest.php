<?php
namespace eMapper\MySQL;

use eMapper\AbstractAssociationTest;

/**
 * 
 * @author emaphp
 * @group association
 * @group mysql
 */
class AssociationTest extends AbstractAssociationTest {
	use MySQLConfig;
	
	public function setUp() {
		$this->mapper = $this->getMapper();
	}
}
?>