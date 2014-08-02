<?php
namespace eMapper\Type;

use eMapper\Reflection\Profiler;
use Acme\Type\RGBColorTypeHandler;

/**
 * Tests default and custom types defined in TypeManager class
 * 
 * @author emaphp
 * @group type
 */
class TypeManagerTest extends \PHPUnit_Framework_TestCase {
	public function testManager1() {
		$typeManager = new TypeManager();
		
		//string
		$this->assertArrayHasKey('string', $typeManager->getTypeHandlers());
		$this->assertInstanceOf('eMapper\Type\Handler\StringTypeHandler', $typeManager->getTypeHandler('string'));
		
		$profile = Profiler::getClassProfile('eMapper\Type\Handler\StringTypeHandler');
		$this->assertFalse($profile->isSafe());
		
		$this->assertArrayHasKey('s', $typeManager->getAliases());
		$this->assertArrayHasKey('str', $typeManager->getAliases());
		$this->assertInstanceOf('eMapper\Type\Handler\StringTypeHandler', $typeManager->getTypeHandler('s'));
		$this->assertInstanceOf('eMapper\Type\Handler\StringTypeHandler', $typeManager->getTypeHandler('str'));
		
		//boolean
		$this->assertArrayHasKey('boolean', $typeManager->getTypeHandlers());
		$this->assertInstanceOf('eMapper\Type\Handler\BooleanTypeHandler', $typeManager->getTypeHandler('boolean'));
		
		$profile = Profiler::getClassProfile('eMapper\Type\Handler\BooleanTypeHandler');
		$this->assertTrue($profile->isSafe());
		
		$this->assertArrayHasKey('b', $typeManager->getAliases());
		$this->assertArrayHasKey('bool', $typeManager->getAliases());
		$this->assertInstanceOf('eMapper\Type\Handler\BooleanTypeHandler', $typeManager->getTypeHandler('b'));
		$this->assertInstanceOf('eMapper\Type\Handler\BooleanTypeHandler', $typeManager->getTypeHandler('bool'));
		
		//integer
		$this->assertArrayHasKey('integer', $typeManager->getTypeHandlers());
		$this->assertInstanceOf('eMapper\Type\Handler\IntegerTypeHandler', $typeManager->getTypeHandler('integer'));
		
		$profile = Profiler::getClassProfile('eMapper\Type\Handler\IntegerTypeHandler');
		$this->assertTrue($profile->isSafe());
		
		$this->assertArrayHasKey('i', $typeManager->getAliases());
		$this->assertArrayHasKey('int', $typeManager->getAliases());
		$this->assertInstanceOf('eMapper\Type\Handler\IntegerTypeHandler', $typeManager->getTypeHandler('int'));
		$this->assertInstanceOf('eMapper\Type\Handler\IntegerTypeHandler', $typeManager->getTypeHandler('i'));
		
		//float
		$this->assertArrayHasKey('float', $typeManager->getTypeHandlers());
		$this->assertInstanceOf('eMapper\Type\Handler\FloatTypeHandler', $typeManager->getTypeHandler('float'));
		
		$profile = Profiler::getClassProfile('eMapper\Type\Handler\FloatTypeHandler');
		$this->assertTrue($profile->isSafe());
		
		$this->assertArrayHasKey('f', $typeManager->getAliases());
		$this->assertArrayHasKey('double', $typeManager->getAliases());
		$this->assertArrayHasKey('real', $typeManager->getAliases());
		$this->assertInstanceOf('eMapper\Type\Handler\FloatTypeHandler', $typeManager->getTypeHandler('f'));
		$this->assertInstanceOf('eMapper\Type\Handler\FloatTypeHandler', $typeManager->getTypeHandler('double'));
		$this->assertInstanceOf('eMapper\Type\Handler\FloatTypeHandler', $typeManager->getTypeHandler('real'));
		
		//blob
		$this->assertArrayHasKey('blob', $typeManager->getTypeHandlers());
		$this->assertInstanceOf('eMapper\Type\Handler\BlobTypeHandler', $typeManager->getTypeHandler('blob'));
		
		$profile = Profiler::getClassProfile('eMapper\Type\Handler\BlobTypeHandler');
		$this->assertTrue($profile->isSafe());
		
		$this->assertArrayHasKey('x', $typeManager->getAliases());
		$this->assertArrayHasKey('bin', $typeManager->getAliases());
		$this->assertInstanceOf('eMapper\Type\Handler\BlobTypeHandler', $typeManager->getTypeHandler('x'));
		$this->assertInstanceOf('eMapper\Type\Handler\BlobTypeHandler', $typeManager->getTypeHandler('bin'));
		
		//datetime
		$this->assertArrayHasKey('DateTime', $typeManager->getTypeHandlers());
		$this->assertInstanceOf('eMapper\Type\Handler\DatetimeTypeHandler', $typeManager->getTypeHandler('DateTime'));
		
		$profile = Profiler::getClassProfile('eMapper\Type\Handler\DatetimeTypeHandler');
		$this->assertFalse($profile->isSafe());
		
		$this->assertArrayHasKey('dt', $typeManager->getAliases());
		$this->assertArrayHasKey('timestamp', $typeManager->getAliases());
		$this->assertInstanceOf('eMapper\Type\Handler\DatetimeTypeHandler', $typeManager->getTypeHandler('dt'));
		$this->assertInstanceOf('eMapper\Type\Handler\DatetimeTypeHandler', $typeManager->getTypeHandler('timestamp'));
		
		//date
		$this->assertArrayHasKey('date', $typeManager->getTypeHandlers());
		$this->assertInstanceOf('eMapper\Type\Handler\DateTypeHandler', $typeManager->getTypeHandler('date'));
		
		$profile = Profiler::getClassProfile('eMapper\Type\Handler\DateTypeHandler');
		$this->assertFalse($profile->isSafe());
		
		$this->assertArrayHasKey('d', $typeManager->getAliases());
		$this->assertInstanceOf('eMapper\Type\Handler\DateTypeHandler', $typeManager->getTypeHandler('d'));
		
		//safe strings
		$this->assertArrayHasKey('sstring', $typeManager->getTypeHandlers());
		$this->assertInstanceOf('eMapper\Type\Handler\SafeStringTypeHandler', $typeManager->getTypeHandler('sstring'));
		
		$profile = Profiler::getClassProfile('eMapper\Type\Handler\SafeStringTypeHandler');
		$this->assertTrue($profile->isSafe());
		
		$this->assertArrayHasKey('ss', $typeManager->getAliases());
		$this->assertArrayHasKey('sstr', $typeManager->getAliases());
		$this->assertInstanceOf('eMapper\Type\Handler\SafeStringTypeHandler', $typeManager->getTypeHandler('ss'));
		$this->assertInstanceOf('eMapper\Type\Handler\SafeStringTypeHandler', $typeManager->getTypeHandler('sstr'));
		
		//json
		$this->assertArrayHasKey('json', $typeManager->getTypeHandlers());
		$this->assertInstanceOf('eMapper\Type\Handler\JSONTypeHandler', $typeManager->getTypeHandler('json'));
		
		$profile = Profiler::getClassProfile('eMapper\Type\Handler\JSONTypeHandler');
		$this->assertFalse($profile->isSafe());
		
		//null
		$this->assertArrayHasKey('null', $typeManager->getTypeHandlers());
		$this->assertInstanceOf('eMapper\Type\Handler\NullTypeHandler', $typeManager->getTypeHandler('null'));
		
		$profile = Profiler::getClassProfile('eMapper\Type\Handler\NullTypeHandler');
		$this->assertTrue($profile->isSafe());
	}
	
	public function testCustomType() {
		$typeManager = new TypeManager();
		$typeManager->setTypeHandler('Acme\RGBColor', new RGBColorTypeHandler());
		$typeManager->addAlias('Acme\RGBColor', 'clr');
		
		$this->assertArrayHasKey('Acme\RGBColor', $typeManager->getTypeHandlers());
		$this->assertInstanceOf('Acme\Type\RGBColorTypeHandler', $typeManager->getTypeHandler('Acme\RGBColor'));
		
		$this->assertArrayHasKey('clr', $typeManager->getAliases());
		$this->assertInstanceOf('Acme\Type\RGBColorTypeHandler', $typeManager->getTypeHandler('clr'));
	}
}
?>