<?php
namespace eMapper\PostgreSQL\Attribute;

use eMapper\PostgreSQL\PostgreSQLConfig;
use eMapper\Attribute\AbstractConditionalAttributeTest;

/**
 * Tests a conditional attribute
 * @author emaphp
 * @group attribute
 * @group postgre
 */
class ConditionalAttributeTest extends AbstractConditionalAttributeTest {
	use PostgreSQLConfig;	
}
?>