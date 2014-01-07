<?php
namespace eMapper\Type;

use eMapper\Reflection\Profiler;

/**
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
		
		$profile = Profiler::getClassAnnotations('eMapper\Type\Handler\StringTypeHandler');
		$this->assertFalse($profile->has('unquoted'));
		
		$this->assertEquals('string', $typeManager->aliases['s']);
		$this->assertEquals('string', $typeManager->aliases['str']);
		
		//boolean
		$this->assertArrayHasKey('boolean', $typeManager->typeHandlers);
		$this->assertInstanceOf('eMapper\Type\Handler\BooleanTypeHandler', $typeManager->typeHandlers['boolean']);
		
		$profile = Profiler::getClassAnnotations('eMapper\Type\Handler\BooleanTypeHandler');
		$this->assertTrue($profile->has('unquoted'));
		
		$this->assertEquals('boolean', $typeManager->aliases['b']);
		$this->assertEquals('boolean', $typeManager->aliases['bool']);
		
		//integer
		$this->assertArrayHasKey('integer', $typeManager->typeHandlers);
		$this->assertInstanceOf('eMapper\Type\Handler\IntegerTypeHandler', $typeManager->typeHandlers['integer']);
		
		$profile = Profiler::getClassAnnotations('eMapper\Type\Handler\IntegerTypeHandler');
		$this->assertTrue($profile->has('unquoted'));
		
		$this->assertEquals('integer', $typeManager->aliases['i']);
		$this->assertEquals('integer', $typeManager->aliases['int']);
		
		//float
		$this->assertArrayHasKey('float', $typeManager->typeHandlers);
		$this->assertInstanceOf('eMapper\Type\Handler\FloatTypeHandler', $typeManager->typeHandlers['float']);
		
		$profile = Profiler::getClassAnnotations('eMapper\Type\Handler\FloatTypeHandler');
		$this->assertTrue($profile->has('unquoted'));
		
		$this->assertEquals('float', $typeManager->aliases['f']);
		$this->assertEquals('float', $typeManager->aliases['double']);
		$this->assertEquals('float', $typeManager->aliases['real']);
		
		//blob
		$this->assertArrayHasKey('blob', $typeManager->typeHandlers);
		$this->assertInstanceOf('eMapper\Type\Handler\BlobTypeHandler', $typeManager->typeHandlers['blob']);
		
		$profile = Profiler::getClassAnnotations('eMapper\Type\Handler\BlobTypeHandler');
		$this->assertTrue($profile->has('unquoted'));
		
		$this->assertEquals('blob', $typeManager->aliases['x']);
		$this->assertEquals('blob', $typeManager->aliases['bin']);
		
		//datetime
		$this->assertArrayHasKey('DateTime', $typeManager->typeHandlers);
		$this->assertInstanceOf('eMapper\Type\Handler\DatetimeTypeHandler', $typeManager->typeHandlers['DateTime']);
		
		$profile = Profiler::getClassAnnotations('eMapper\Type\Handler\DatetimeTypeHandler');
		$this->assertFalse($profile->has('unquoted'));
		
		$this->assertEquals('DateTime', $typeManager->aliases['dt']);
		$this->assertEquals('DateTime', $typeManager->aliases['timestamp']);
		
		//date
		$this->assertArrayHasKey('date', $typeManager->typeHandlers);
		$this->assertInstanceOf('eMapper\Type\Handler\DateTypeHandler', $typeManager->typeHandlers['date']);
		
		$profile = Profiler::getClassAnnotations('eMapper\Type\Handler\DateTypeHandler');
		$this->assertFalse($profile->has('unquoted'));
		
		$this->assertEquals('date', $typeManager->aliases['d']);
		
		//unquoted strings
		$this->assertArrayHasKey('ustring', $typeManager->typeHandlers);
		$this->assertInstanceOf('eMapper\Type\Handler\UnquotedStringTypeHandler', $typeManager->typeHandlers['ustring']);
		
		$profile = Profiler::getClassAnnotations('eMapper\Type\Handler\UnquotedStringTypeHandler');
		$this->assertTrue($profile->has('unquoted'));
		
		$this->assertEquals('ustring', $typeManager->aliases['us']);
		$this->assertEquals('ustring', $typeManager->aliases['ustr']);
		
		//null
		$this->assertArrayHasKey('null', $typeManager->typeHandlers);
		$this->assertInstanceOf('eMapper\Type\Handler\NullTypeHandler', $typeManager->typeHandlers['null']);
		
		$profile = Profiler::getClassAnnotations('eMapper\Type\Handler\NullTypeHandler');
		$this->assertTrue($profile->has('unquoted'));
	}
}
?>