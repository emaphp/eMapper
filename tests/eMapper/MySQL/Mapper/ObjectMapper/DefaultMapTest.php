<?php
namespace eMapper\MySQL\Mapper\ObjectMapper;

use eMapper\MySQL\MySQLConfig;
use eMapper\Mapper\ObjectMapper\AbstractDefaultMapTest;

/**
 * Tests Mapper class obtaining default stdClass instances
 * @author emaphp
 * @group mysql
 * @group mapper
 */
class DefaultMapTest extends AbstractDefaultMapTest {
	use MySQLConfig;
}
?>