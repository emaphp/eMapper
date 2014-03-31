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
		$this->assertArrayHasKey('string', $typeManager->typeHandlers);
		$this->assertInstanceOf('eMapper\Type\Handler\StringTypeHandler', $typeManager->typeHandlers['string']);
		
		$profile = Profiler::getClassProfile('eMapper\Type\Handler\StringTypeHandler')->classAnnotations;
		$this->assertFalse($profile->has('map.unquoted'));
		
		$this->assertEquals('string', $typeManager->aliases['s']);
		$this->assertEquals('string', $typeManager->aliases['str']);
		
		//boolean
		$this->assertArrayHasKey('boolean', $typeManager->typeHandlers);
		$this->assertInstanceOf('eMapper\Type\Handler\BooleanTypeHandler', $typeManager->typeHandlers['boolean']);
		
		$profile = Profiler::getClassProfile('eMapper\Type\Handler\BooleanTypeHandler')->classAnnotations;
		$this->assertTrue($profile->has('map.unquoted'));
		
		$this->assertEquals('boolean', $typeManager->aliases['b']);
		$this->assertEquals('boolean', $typeManager->aliases['bool']);
		
		//integer
		$this->assertArrayHasKey('integer', $typeManager->typeHandlers);
		$this->assertInstanceOf('eMapper\Type\Handler\IntegerTypeHandler', $typeManager->typeHandlers['integer']);
		
		$profile = Profiler::getClassProfile('eMapper\Type\Handler\IntegerTypeHandler')->classAnnotations;
		$this->assertTrue($profile->has('map.unquoted'));
		
		$this->assertEquals('integer', $typeManager->aliases['i']);
		$this->assertEquals('integer', $typeManager->aliases['int']);
		
		//float
		$this->assertArrayHasKey('float', $typeManager->typeHandlers);
		$this->assertInstanceOf('eMapper\Type\Handler\FloatTypeHandler', $typeManager->typeHandlers['float']);
		
		$profile = Profiler::getClassProfile('eMapper\Type\Handler\FloatTypeHandler')->classAnnotations;
		$this->assertTrue($profile->has('map.unquoted'));
		
		$this->assertEquals('float', $typeManager->aliases['f']);
		$this->assertEquals('float', $typeManager->aliases['double']);
		$this->assertEquals('float', $typeManager->aliases['real']);
		
		//blob
		$this->assertArrayHasKey('blob', $typeManager->typeHandlers);
		$this->assertInstanceOf('eMapper\Type\Handler\BlobTypeHandler', $typeManager->typeHandlers['blob']);
		
		$profile = Profiler::getClassProfile('eMapper\Type\Handler\BlobTypeHandler')->classAnnotations;
		$this->assertTrue($profile->has('map.unquoted'));
		
		$this->assertEquals('blob', $typeManager->aliases['x']);
		$this->assertEquals('blob', $typeManager->aliases['bin']);
		
		//datetime
		$this->assertArrayHasKey('DateTime', $typeManager->typeHandlers);
		$this->assertInstanceOf('eMapper\Type\Handler\DatetimeTypeHandler', $typeManager->typeHandlers['DateTime']);
		
		$profile = Profiler::getClassProfile('eMapper\Type\Handler\DatetimeTypeHandler')->classAnnotations;
		$this->assertFalse($profile->has('map.unquoted'));
		
		$this->assertEquals('DateTime', $typeManager->aliases['dt']);
		$this->assertEquals('DateTime', $typeManager->aliases['timestamp']);
		
		//date
		$this->assertArrayHasKey('date', $typeManager->typeHandlers);
		$this->assertInstanceOf('eMapper\Type\Handler\DateTypeHandler', $typeManager->typeHandlers['date']);
		
		$profile = Profiler::getClassProfile('eMapper\Type\Handler\DateTypeHandler')->classAnnotations;
		$this->assertFalse($profile->has('map.unquoted'));
		
		$this->assertEquals('date', $typeManager->aliases['d']);
		
		//unquoted strings
		$this->assertArrayHasKey('ustring', $typeManager->typeHandlers);
		$this->assertInstanceOf('eMapper\Type\Handler\UnquotedStringTypeHandler', $typeManager->typeHandlers['ustring']);
		
		$profile = Profiler::getClassProfile('eMapper\Type\Handler\UnquotedStringTypeHandler')->classAnnotations;
		$this->assertTrue($profile->has('map.unquoted'));
		
		$this->assertEquals('ustring', $typeManager->aliases['us']);
		$this->assertEquals('ustring', $typeManager->aliases['ustr']);
		
		//string
		$this->assertArrayHasKey('json', $typeManager->typeHandlers);
		$this->assertInstanceOf('eMapper\Type\Handler\JSONTypeHandler', $typeManager->typeHandlers['json']);
		
		$profile = Profiler::getClassProfile('eMapper\Type\Handler\JSONTypeHandler')->classAnnotations;
		$this->assertFalse($profile->has('map.unquoted'));
		
		//null
		$this->assertArrayHasKey('null', $typeManager->typeHandlers);
		$this->assertInstanceOf('eMapper\Type\Handler\NullTypeHandler', $typeManager->typeHandlers['null']);
		
		$profile = Profiler::getClassProfile('eMapper\Type\Handler\NullTypeHandler')->classAnnotations;
		$this->assertTrue($profile->has('map.unquoted'));
	}
	
	public function testCustomType() {
		$typeManager = new TypeManager();
		$typeManager->setTypeHandler('Acme\RGBColor', new RGBColorTypeHandler());
		$typeManager->addAlias('Acme\RGBColor', 'clr');
		
		$this->assertArrayHasKey('Acme\RGBColor', $typeManager->typeHandlers);
		$this->assertInstanceOf('Acme\Type\RGBColorTypeHandler', $typeManager->typeHandlers['Acme\RGBColor']);
		
		$this->assertArrayHasKey('clr', $typeManager->aliases);
		$this->assertEquals('Acme\RGBColor', $typeManager->aliases['clr']);
	}
}
?>