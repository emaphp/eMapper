<?php
namespace eMapper\SQLite\Mapper\ScalarMapper;

use eMapper\SQLite\SQLiteConfig;
use eMapper\Mapper\ScalarMapper\AbstractDatetimeTypeTest;

/**
 * Test SQLiteMapper with date values
 * @author emaphp
 * @group sqlite
 * @group mapper
 * @group date
 */
class DatetimeTypeTest extends AbstractDatetimeTypeTest {
	use SQLiteConfig;
	
	public function testDatetime() {
		$value = $this->mapper->type('DateTime')->query("SELECT CURRENT_TIMESTAMP");
		$this->assertInstanceOf('DateTime', $value);
		$this->assertRegExp('/([\d]{4})-([\d]{2})-([\d]{2}) ([\d]{2}):([\d]{2}):([\d]{2})/', $value->format('Y-m-d H:i:s'));
	
		$value = $this->mapper->type('dt')->query("SELECT last_login FROM users WHERE user_id = 1");
		$this->assertInstanceOf('DateTime', $value);
		$this->assertEquals('2013-08-10 19:57:15', $value->format('Y-m-d H:i:s'));
	
		$value = $this->mapper->type('date', 'last_login')->query("SELECT * FROM users WHERE user_id = 3");
		$this->assertInstanceOf('DateTime', $value);
		$this->assertEquals('2013-02-16 20:00:33', $value->format('Y-m-d H:i:s'));
	
		$result = $this->mapper->type('timestamp')->query("SELECT sale_date FROM sales WHERE sale_id = 1");
		$this->assertInstanceOf('DateTime', $result);
		$this->assertEquals('2013-08-10 20:37:18', $result->format('Y-m-d H:i:s'));
	
		///WARNING!!!!!! 2011 -> 20:11
		$result = $this->mapper->type('DateTime')->query("SELECT manufacture_year FROM products WHERE product_id = 1");
		$this->assertInstanceOf('DateTime', $result);
		$this->assertEquals('20:11', $result->format('H:i'));
	
		$result = $this->mapper->type('DateTime')->query("SELECT newsletter_time FROM users WHERE user_id = 3");
		$this->assertInstanceOf('DateTime', $result);
		$this->assertEquals('17:00:00', $result->format('H:i:s'));
	}
}
?>