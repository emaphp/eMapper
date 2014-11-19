<?php
namespace eMapper\Query;

use eMapper\Reflection\Profile\ClassProfile;

/**
 * 
 * @author emaphp
 * @group attribute
 */
class AttrTest extends \PHPUnit_Framework_TestCase {
	protected $profile;
	
	public function setUp() {
		$this->profile = new ClassProfile('Acme\Entity\Product');
	}
	
	public function testAttribute() {
		$attr = Attr::category();
		$this->assertInstanceOf('eMapper\Query\Attr', $attr);
		$this->assertEquals('category', $attr->getName());
	}
	
	public function testColumn() {
		$column = Column::product_id();
		$this->assertInstanceOf('eMapper\Query\Column', $column);
		$this->assertEquals('product_id', $column->getName());
	}
	
	public function testAttributeColumnName() {
		$attr = Attr::category();
		$this->assertEquals('category', $attr->getColumnName($this->profile));
	}
	
	public function testColumnName() {
		$column = Column::product_code();
		$this->assertEquals('product_code', $column->getColumnName($this->profile));
	}
	
	public function testDiffAttributeColumnName() {
		$attr = Attr::id();
		$this->assertEquals('product_id', $attr->getColumnName($this->profile));
	}
	
	/**
	 * @expectedException \RuntimeException
	 */
	public function testMissingAttributeColumnName() {
		$attr = Attr::notfound();
		$this->assertEquals('', $attr->getColumnName($this->profile));
	}
	
	public function testMissingColumnName() {
		$column = Column::notfound();
		$this->assertEquals('notfound', $column->getColumnName($this->profile));
	}
	
	public function testAttributeMissingType() {
		$name = Attr::name();
		$this->assertFalse($name->hasType());
		$this->assertNull($name->getType());
	}
	
	public function testAttributeType() {
		$birthDate = Attr::birthDate('string');
		$this->assertTrue($birthDate->hasType());
		$this->assertEquals('string', $birthDate->getType());
	}
	
	public function testColumnMissingType() {
		$name = Column::name();
		$this->assertFalse($name->hasType());
		$this->assertNull($name->getType());
	}
	
	public function testColumnType() {
		$birth_date = Column::birth_date('string');
		$this->assertTrue($birth_date->hasType());
		$this->assertEquals('string', $birth_date->getType());
	}
	
	//lookup methods
	public function testEqual() {
		$eq = Attr::id()->eq(1);
		$this->assertInstanceOf('eMapper\SQL\Predicate\Equal', $eq);
		$this->assertEquals('id', $eq->getField()->getName());
		$this->assertFalse($eq->getNegate());
		$this->assertEquals(1, $eq->getExpression());
	}
	
	public function testNotEqual() {
		$eq = Attr::name()->eq('emma', false);
		$this->assertInstanceOf('eMapper\SQL\Predicate\Equal', $eq);
		$this->assertEquals('name', $eq->getField()->getName());
		$this->assertTrue($eq->getNegate());
		$this->assertEquals('emma', $eq->getExpression());
	}
	
	public function testContains() {
		$contains = Attr::title()->contains('The');
		$this->assertInstanceOf('eMapper\SQL\Predicate\Contains', $contains);
		$this->assertEquals('title', $contains->getField()->getName());
		$this->assertFalse($contains->getNegate());
		$this->assertEquals('The', $contains->getExpression());
		$this->assertTrue($contains->getCaseSensitive());
	}
	
	public function testNotContains() {
		$contains = Attr::title()->contains('The', false);
		$this->assertInstanceOf('eMapper\SQL\Predicate\Contains', $contains);
		$this->assertEquals('title', $contains->getField()->getName());
		$this->assertTrue($contains->getNegate());
		$this->assertEquals('The', $contains->getExpression());
		$this->assertTrue($contains->getCaseSensitive());
	}
	
	public function testIContains() {
		$contains = Attr::description()->icontains('phone');
		$this->assertInstanceOf('eMapper\SQL\Predicate\Contains', $contains);
		$this->assertEquals('description', $contains->getField()->getName());
		$this->assertFalse($contains->getNegate());
		$this->assertEquals('phone', $contains->getExpression());
		$this->assertFalse($contains->getCaseSensitive());
	}
	
	public function testNotIContains() {
		$contains = Attr::description()->icontains('phone', false);
		$this->assertInstanceOf('eMapper\SQL\Predicate\Contains', $contains);
		$this->assertEquals('description', $contains->getField()->getName());
		$this->assertTrue($contains->getNegate());
		$this->assertEquals('phone', $contains->getExpression());
		$this->assertFalse($contains->getCaseSensitive());
	}
	
	public function testIn() {
		$in = Attr::id()->in([1, 2, 3]);
		$this->assertInstanceOf('eMapper\SQL\Predicate\In', $in);
		$this->assertEquals('id', $in->getField()->getName());
		$this->assertFalse($in->getNegate());
		$this->assertEquals([1, 2, 3], $in->getExpression());
	}
	
	public function testNotIn() {
		$in = Attr::id()->in([1, 2, 3], false);
		$this->assertInstanceOf('eMapper\SQL\Predicate\In', $in);
		$this->assertEquals('id', $in->getField()->getName());
		$this->assertTrue($in->getNegate());
		$this->assertEquals([1, 2, 3], $in->getExpression());
	}
	
	public function testGreaterThan() {
		$gt = Attr::price()->gt(50);
		$this->assertInstanceOf('eMapper\SQL\Predicate\GreaterThan', $gt);
		$this->assertEquals('price', $gt->getField()->getName());
		$this->assertFalse($gt->getNegate());
		$this->assertEquals(50, $gt->getExpression());
	}
	
	public function testNotGreaterThan() {
		$gt = Attr::price()->gt(50, false);
		$this->assertInstanceOf('eMapper\SQL\Predicate\GreaterThan', $gt);
		$this->assertEquals('price', $gt->getField()->getName());
		$this->assertTrue($gt->getNegate());
		$this->assertEquals(50, $gt->getExpression());
	}
	
	public function testGreaterThanEqual() {
		$gte = Attr::price()->gte(50);
		$this->assertInstanceOf('eMapper\SQL\Predicate\GreaterThanEqual', $gte);
		$this->assertEquals('price', $gte->getField()->getName());
		$this->assertFalse($gte->getNegate());
		$this->assertEquals(50, $gte->getExpression());
	}
	
	public function testNotGreaterThanEqual() {
		$gte = Attr::price()->gte(50, false);
		$this->assertInstanceOf('eMapper\SQL\Predicate\GreaterThanEqual', $gte);
		$this->assertEquals('price', $gte->getField()->getName());
		$this->assertTrue($gte->getNegate());
		$this->assertEquals(50, $gte->getExpression());
	}
	
	public function testLessThan() {
		$lt = Attr::price()->lt(50);
		$this->assertInstanceOf('eMapper\SQL\Predicate\LessThan', $lt);
		$this->assertEquals('price', $lt->getField()->getName());
		$this->assertFalse($lt->getNegate());
		$this->assertEquals(50, $lt->getExpression());
	}
	
	public function testNotLessThan() {
		$lt = Attr::price()->lt(50, false);
		$this->assertInstanceOf('eMapper\SQL\Predicate\LessThan', $lt);
		$this->assertEquals('price', $lt->getField()->getName());
		$this->assertTrue($lt->getNegate());
		$this->assertEquals(50, $lt->getExpression());
	}
	
	public function testLessThanEqual() {
		$lte = Attr::price()->lte(50);
		$this->assertInstanceOf('eMapper\SQL\Predicate\LessThanEqual', $lte);
		$this->assertEquals('price', $lte->getField()->getName());
		$this->assertFalse($lte->getNegate());
		$this->assertEquals(50, $lte->getExpression());
	}
	
	public function testNotLessThanEqual() {
		$lte = Attr::price()->lte(50, false);
		$this->assertInstanceOf('eMapper\SQL\Predicate\LessThanEqual', $lte);
		$this->assertEquals('price', $lte->getField()->getName());
		$this->assertTrue($lte->getNegate());
		$this->assertEquals(50, $lte->getExpression());
	}
	
	public function testStartsWith() {
		$sw = Attr::title()->startswith('My');
		$this->assertInstanceOf('eMapper\SQL\Predicate\StartsWith', $sw);
		$this->assertEquals('title', $sw->getField()->getName());
		$this->assertFalse($sw->getNegate());
		$this->assertEquals('My', $sw->getExpression());
		$this->assertTrue($sw->getCaseSensitive());
	}
	
	public function testNotStartsWith() {
		$sw = Attr::title()->startswith('My', false);
		$this->assertInstanceOf('eMapper\SQL\Predicate\StartsWith', $sw);
		$this->assertEquals('title', $sw->getField()->getName());
		$this->assertTrue($sw->getNegate());
		$this->assertEquals('My', $sw->getExpression());
		$this->assertTrue($sw->getCaseSensitive());
	}
	
	public function testIStartsWith() {
		$sw = Attr::title()->istartswith('my');
		$this->assertInstanceOf('eMapper\SQL\Predicate\StartsWith', $sw);
		$this->assertEquals('title', $sw->getField()->getName());
		$this->assertFalse($sw->getNegate());
		$this->assertEquals('my', $sw->getExpression());
		$this->assertFalse($sw->getCaseSensitive());
	}
	
	public function testNotIStartsWith() {
		$sw = Attr::title()->istartswith('my', false);
		$this->assertInstanceOf('eMapper\SQL\Predicate\StartsWith', $sw);
		$this->assertEquals('title', $sw->getField()->getName());
		$this->assertTrue($sw->getNegate());
		$this->assertEquals('my', $sw->getExpression());
		$this->assertFalse($sw->getCaseSensitive());
	}
	
	public function testEndsWith() {
		$ew = Attr::content()->endswith('bye');
		$this->assertInstanceOf('eMapper\SQL\Predicate\EndsWith', $ew);
		$this->assertEquals('content', $ew->getField()->getName());
		$this->assertFalse($ew->getNegate());
		$this->assertEquals('bye', $ew->getExpression());
		$this->assertTrue($ew->getCaseSensitive());
	}
	
	public function testNotEndsWith() {
		$ew = Attr::content()->endswith('bye', false);
		$this->assertInstanceOf('eMapper\SQL\Predicate\EndsWith', $ew);
		$this->assertEquals('content', $ew->getField()->getName());
		$this->assertTrue($ew->getNegate());
		$this->assertEquals('bye', $ew->getExpression());
		$this->assertTrue($ew->getCaseSensitive());
	}
	
	public function testIEndsWith() {
		$ew = Attr::content()->iendswith('bye');
		$this->assertInstanceOf('eMapper\SQL\Predicate\EndsWith', $ew);
		$this->assertEquals('content', $ew->getField()->getName());
		$this->assertFalse($ew->getNegate());
		$this->assertEquals('bye', $ew->getExpression());
		$this->assertFalse($ew->getCaseSensitive());
	}
	
	public function testNotIEndsWith() {
		$ew = Attr::content()->iendswith('bye', false);
		$this->assertInstanceOf('eMapper\SQL\Predicate\EndsWith', $ew);
		$this->assertEquals('content', $ew->getField()->getName());
		$this->assertTrue($ew->getNegate());
		$this->assertEquals('bye', $ew->getExpression());
		$this->assertFalse($ew->getCaseSensitive());
	}
	
	public function testRange() {
		$range = Attr::price()->range(10, 35);
		$this->assertInstanceOf('eMapper\SQL\Predicate\Range', $range);
		$this->assertEquals('price', $range->getField()->getName());
		$this->assertFalse($range->getNegate());
		$this->assertEquals(10, $range->getFrom());
		$this->assertEquals(35, $range->getTo());
	}
	
	public function testNotRange() {
		$range = Attr::price()->range(10, 35, false);
		$this->assertInstanceOf('eMapper\SQL\Predicate\Range', $range);
		$this->assertEquals('price', $range->getField()->getName());
		$this->assertTrue($range->getNegate());
		$this->assertEquals(10, $range->getFrom());
		$this->assertEquals(35, $range->getTo());
	}
	
	public function testRegex() {
		$regex = Attr::content()->matches('^The');
		$this->assertInstanceOf('eMapper\SQL\Predicate\Regex', $regex);
		$this->assertEquals('content', $regex->getField()->getName());
		$this->assertFalse($regex->getNegate());
		$this->assertEquals('^The', $regex->getExpression());
		$this->assertTrue($regex->getCaseSensitive());
	}
	
	public function testNotRegex() {
		$regex = Attr::content()->matches('^The', false);
		$this->assertInstanceOf('eMapper\SQL\Predicate\Regex', $regex);
		$this->assertEquals('content', $regex->getField()->getName());
		$this->assertTrue($regex->getNegate());
		$this->assertEquals('^The', $regex->getExpression());
		$this->assertTrue($regex->getCaseSensitive());
	}
	
	public function testIRegex() {
		$regex = Attr::content()->imatches('^The');
		$this->assertInstanceOf('eMapper\SQL\Predicate\Regex', $regex);
		$this->assertEquals('content', $regex->getField()->getName());
		$this->assertFalse($regex->getNegate());
		$this->assertEquals('^The', $regex->getExpression());
		$this->assertFalse($regex->getCaseSensitive());
	}
	
	public function testNotIRegex() {
		$regex = Attr::content()->imatches('^The', false);
		$this->assertInstanceOf('eMapper\SQL\Predicate\Regex', $regex);
		$this->assertEquals('content', $regex->getField()->getName());
		$this->assertTrue($regex->getNegate());
		$this->assertEquals('^The', $regex->getExpression());
		$this->assertFalse($regex->getCaseSensitive());
	}
	
	public function testNull() {
		$n = Attr::profile_id()->isnull();
		$this->assertInstanceOf('eMapper\SQL\Predicate\IsNull', $n);
		$this->assertEquals('profile_id', $n->getField()->getName());
		$this->assertFalse($n->getNegate());
	}
	
	public function testNotNull() {
		$n = Attr::profile_id()->isnull(false);
		$this->assertInstanceOf('eMapper\SQL\Predicate\IsNull', $n);
		$this->assertEquals('profile_id', $n->getField()->getName());
		$this->assertTrue($n->getNegate());
	}
}
?>