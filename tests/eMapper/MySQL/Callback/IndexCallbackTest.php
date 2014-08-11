<?php
namespace eMapper\MySQL\Callback;

use eMapper\MySQL\MySQLConfig;
use eMapper\Callback\AbstractIndexCallbackTest;

/**
 * Test setting an index callback
 * 
 * @author emaphp
 * @group callback
 * @group mysql
 */
class IndexCallbackTest extends AbstractIndexCallbackTest {
	use MySQLConfig;
}
?>