<?php
namespace eMapper\MySQL\Attribute;

use eMapper\Attribute\AbstractConditionalAttributeTest;
use eMapper\MySQL\MySQLConfig;

/**
 * Test a conditional attribute
 * 
 * @author emaphp
 * @group attribute
 * @group mysql
 */
class ConditionalAttributeTest extends AbstractConditionalAttributeTest {
	use MySQLConfig;
}
?>