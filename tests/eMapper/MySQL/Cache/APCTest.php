<?php
namespace eMapper\MySQL\Cache;

use eMapper\MySQL\MySQLConfig;
use eMapper\Cache\APC\AbstractAPCTest;

/**
 * Test APCProvider with a MySQL connection
 * @author emaphp
 * @group mysql
 * @group cache
 * @group apc
 */
class APCTest extends AbstractAPCTest {
	use MySQLConfig;
	
	public function getPrefix() {
		return 'mysql_';
	}
}
?>
