<?php
namespace eMapper\MySQL\Callback;

use eMapper\MySQL\MySQLConfig;
use eMapper\Callback\AbstractGroupCallbackTest;

/**
 * Test setting a grouping callback
 * 
 * @author emaphp
 * @group callback
 * @group mysql
 */
class GroupCallbackTest extends AbstractGroupCallbackTest {
	use MySQLConfig;
}
?>