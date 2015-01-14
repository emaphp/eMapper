<?php
namespace eMapper\MySQL\Mapper\ScalarMapper;

use eMapper\MySQL\MySQLConfig;
use eMapper\Mapper\ScalarMapper\AbstractDatetimeTypeTest;

/**
 * Test Mapper class with date values
 * @author emaphp
 * @group mysql
 * @group mapper
 * @group date
 */
class DatetimeTypeTest extends AbstractDatetimeTypeTest {
	use MySQLConfig;
	
	public function testDatetime() {
		$value = $this->mapper->type('DateTime')->query("SELECT NOW()");
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
		$this->assertEquals('2013-08-10 23:37:18', $result->format('Y-m-d H:i:s'));
	
		///WARNING!!!!!! 2011 -> 20:11
		$result = $this->mapper->type('DateTime')->query("SELECT manufacture_year FROM products WHERE product_id = 1");
		$this->assertInstanceOf('DateTime', $result);
		$this->assertEquals('20:11', $result->format('H:i'));
	
		$result = $this->mapper->type('DateTime')->query("SELECT newsletter_time FROM users WHERE user_id = 3");
		$this->assertInstanceOf('DateTime', $result);
		$this->assertEquals('17:00:00', $result->format('H:i:s'));
	}
	
	public function testDatetimeColumn() {
		$value = $this->mapper->type('DateTime', 'sale_date')->query("SELECT * FROM sales WHERE sale_id = 2");
	
		$this->assertInstanceOf('\DateTime', $value);
		$this->assertEquals('2013-05-17 17:22:50', $value->format('Y-m-d H:i:s'));
	}
}
?>