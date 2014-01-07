<?php
namespace eMapper;

use Acme\Type\ValueExporter;
use Acme\Type\ValueCollection;

/**
 * 
 * @author emaphp
 * @group value
 */
class ValueExportTest extends \PHPUnit_Framework_TestCase {
	public function testToString() {
		$ve = new ValueExporter();
		$this->assertNull($ve->toString(null));
		$this->assertEquals('100', $ve->toString('100'));
		$this->assertEquals('100', $ve->toString(100));
		$this->assertEquals('10.5', $ve->toString(10.5));
		$this->assertEquals('1', $ve->toString(true));
		$this->assertEquals('', $ve->toString(false));
		
		$fp = fopen(__DIR__ . '/resource', 'r');
		$this->assertFalse($ve->toString($fp));
		fclose($fp);
		
		$this->assertFalse($ve->toString(array(1, 2, 3)));
		$this->assertFalse($ve->toString(new \stdClass()));
		$this->assertEquals('1,2,3', $ve->toString(new ValueCollection(array(1,2,3))));
	}
	
	public function testAsString() {
		$ve = new ValueExporter();
		$this->assertEquals('NULL', $ve->asString(null));
		$this->assertEquals('100', $ve->asString('100'));
		$this->assertEquals('100', $ve->asString(100));
		$this->assertEquals('10.5', $ve->asString(10.5));
		$this->assertEquals('TRUE', $ve->asString(true));
		$this->assertEquals('FALSE', $ve->asString(false));
		
		$fp = fopen(__DIR__ . '/resource', 'r');
		$this->assertFalse($ve->toString($fp));
		fclose($fp);
		
		$this->assertFalse($ve->asString(array(1, 2, 3)));
		$this->assertFalse($ve->asString(new \stdClass()));
		$this->assertEquals('1,2,3', $ve->asString(new ValueCollection(array(1,2,3))));
	}
}
?>