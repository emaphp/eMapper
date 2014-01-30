<?php
namespace eMapper;

use Acme\Type\RGBColorTypeHandler;
use Acme\RGBColor;

/**
 * Tests a custom type handler class
 * 
 * @author emaphp
 * @group typehandler
 */
class TypeHandlerTest extends \PHPUnit_Framework_TestCase {
	public function testSetParameter() {
		$th = new RGBColorTypeHandler();
		
		$color = new RGBColor(255, 0, 0);
		$parameter = $th->setParameter($color);
		$this->assertEquals('ff0000', $parameter);
		
		$color = new RGBColor(0, 255, 0);
		$parameter = $th->setParameter($color);
		$this->assertEquals('00ff00', $parameter);
		
		$color = new RGBColor(0, 0, 255);
		$parameter = $th->setParameter($color);
		$this->assertEquals('0000ff', $parameter);
	}
	
	public function testGetValue() {
		$th = new RGBColorTypeHandler();
		
		$color = $th->getValue('FF0065');
		$this->assertInstanceOf('Acme\\RGBColor', $color);
		$this->assertEquals(255, $color->red);
		$this->assertEquals(0, $color->green);
		$this->assertEquals(101, $color->blue);
		
		$color = $th->getValue('baFF00');
		$this->assertInstanceOf('Acme\\RGBColor', $color);
		$this->assertEquals(186, $color->red);
		$this->assertEquals(255, $color->green);
		$this->assertEquals(0, $color->blue);
		
		$color = $th->getValue('0060FF');
		$this->assertInstanceOf('Acme\\RGBColor', $color);
		$this->assertEquals(0, $color->red);
		$this->assertEquals(96, $color->green);
		$this->assertEquals(255, $color->blue);
	}
}
?>