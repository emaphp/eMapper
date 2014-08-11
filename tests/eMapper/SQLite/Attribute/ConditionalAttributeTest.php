<?php
namespace eMapper\SQLite\Attribute;

use eMapper\SQLite\SQLiteConfig;
use eMapper\Attribute\AbstractConditionalAttributeTest;

/**
 * Tests a conditional attribute
 * @author emaphp
 * @group attribute
 * @group sqlite
 */
class ConditionalAttributeTest extends AbstractConditionalAttributeTest {
	use SQLiteConfig;
}
?>