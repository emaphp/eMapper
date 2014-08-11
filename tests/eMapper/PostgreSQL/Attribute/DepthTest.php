<?php
namespace eMapper\PostgreSQL\Attribute;

use eMapper\Attribute\AbstractDepthTest;
use eMapper\PostgreSQL\PostgreSQLConfig;

/**
 * Test setting different values for depth internal value
 *
 * @author emaphp
 * @group postgre
 * @group depth
 */
class DepthTest extends AbstractDepthTest {
	use PostgreSQLConfig;
}
?>