<?php
namespace eMapper;

use Acme\Type\ValueExporter;
use Acme\Type\ValueCollection;

/**
 * Tests the ValueExport trait conversion methods
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
		
		$fp = fopen(__DIR__ . '/avatar.gif', 'r');
		$this->assertFalse($ve->toString($fp));
		fclose($fp);
		
		$this->assertFalse($ve->toString(array(1, 2, 3)));
		$this->assertFalse($ve->toString(new \stdClass()));
		$this->assertEquals('1,2,3', $ve->toString(new ValueCollection(array(1,2,3))));
	}
}
?>