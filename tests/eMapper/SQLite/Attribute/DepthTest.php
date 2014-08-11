<?php
namespace eMapper\SQLite\Attribute;

use eMapper\Attribute\AbstractDepthTest;
use eMapper\SQLite\SQLiteConfig;

/**
 * Tests setting different values for depth internal value
 * @author emaphp
 * @group sqlite
 * @group depth
 */
class DepthTest extends AbstractDepthTest {
	use SQLiteConfig;
}

?>