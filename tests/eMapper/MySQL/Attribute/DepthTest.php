<?php
namespace eMapper\MySQL\Attribute;

use eMapper\Attribute\AbstractDepthTest;
use eMapper\MySQL\MySQLConfig;

/**
 * Test setting different values for depth internal value
 * 
 * @author emaphp
 * @group mysql
 * @group depth
 */
class DepthTest extends AbstractDepthTest {
	use MySQLConfig;
}
?>