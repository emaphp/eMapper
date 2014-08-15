<?php
namespace eMapper\PostgreSQL\Mapper\ObjectMapper;

use eMapper\PostgreSQL\PostgreSQLConfig;
use eMapper\Mapper\ObjectMapper\AbstractEntityTest;

/**
 * Tests Mapper class mapping to entities
 * @author emaphp
 * @group postgre
 * @group mapper
 */
class EntityTest extends AbstractEntityTest {
	use PostgreSQLConfig;
}
?>