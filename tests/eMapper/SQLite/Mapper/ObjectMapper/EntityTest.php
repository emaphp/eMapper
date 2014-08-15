<?php
namespace eMapper\SQLite\Mapper\ObjectMapper;

use eMapper\SQLite\SQLiteConfig;
use eMapper\Mapper\ObjectMapper\AbstractEntityTest;

/**
 * Tests SQLiteMapper mapping to entity classes
 * @author emaphp
 * @group sqlite
 * @group mapper
 */
class EntityTest extends AbstractEntityTest {
	use SQLiteConfig;
}
?>