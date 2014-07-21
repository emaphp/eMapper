<?php
namespace eMapper;

use eMapper\Engine\Generic\Driver;
use eMapper\Reflection\Profile\ClassProfile;
use eMapper\Query\Builder\DeleteQueryBuilder;
use eMapper\Query\Attr;
use eMapper\Query\Q;
use eMapper\Query\Column;
use eMapper\Query\Builder\InsertQueryBuilder;
use eMapper\Query\Builder\UpdateQueryBuilder;
use eMapper\Query\Builder\SelectQueryBuilder;

abstract class AbstractQueryTest extends \PHPUnit_Framework_TestCase {
	/**
	 * Engine driver
	 * @var Driver
	 */
	protected $driver;
	
	/**
	 * Class profile
	 * @var ClassProfile
	 */
	protected $profile;
	
	public function setUp() {
		$this->build();
	}
	
	public abstract function build();
	
	protected function assertRegExpMatch($regex, $actual, &$matches) {
		$this->assertRegExp($regex, $actual);
		preg_match($regex, $actual, $matches);
	}
	
	//SELECT
	public function testSelectAll() {
		$query = new SelectQueryBuilder($this->profile);
		list($query, $args) = $query->build($this->driver, []);
		$this->assertEquals("SELECT * FROM @@products", $query);
		$this->assertNull($args);
	}
	
	public function testSelectAllColumns() {
		$query = new SelectQueryBuilder($this->profile);
		$config = ['query.columns' => [Attr::code(), Attr::category()]];
		list($query, $args) = $query->build($this->driver, $config);
		$this->assertEquals("SELECT product_code, category FROM @@products", $query);
		$this->assertNull($args);
	}
	
	public function testSelectAllOrder() {
		$query = new SelectQueryBuilder($this->profile);
		$config = ['query.order' => [Attr::id(), Attr::code('DESC')]];
		list($query, $args) = $query->build($this->driver, $config);
		$this->assertEquals("SELECT * FROM @@products ORDER BY product_id, product_code DESC", $query);
	}
	
	public function testSelectAllLimit() {
		$query = new SelectQueryBuilder($this->profile);
		$config = ['query.from' => 10];
		list($query, $args) = $query->build($this->driver, $config);
		$this->assertEquals("SELECT * FROM @@products LIMIT 10", $query);
	}
	
	public function testSelectAllLimits() {
		$query = new SelectQueryBuilder($this->profile);
		$config = ['query.from' => 5, 'query.to' => 10];
		list($query, $args) = $query->build($this->driver, $config);
		$this->assertEquals("SELECT * FROM @@products LIMIT 5, 10", $query);
	}
	
	public function testSelectAllOrderLimits() {
		$query = new SelectQueryBuilder($this->profile);
		$config = ['query.order' => [Attr::id(), Attr::code('DESC')], 'query.from' => 5, 'query.to' => 10];
		list($query, $args) = $query->build($this->driver, $config);
		$this->assertEquals("SELECT * FROM @@products ORDER BY product_id, product_code DESC LIMIT 5, 10", $query);
	}
	
	public function testSelectAllDistinct() {
		$query = new SelectQueryBuilder($this->profile);
		$config = ['query.distinct' => true, 'query.columns' => [Attr::code(), Attr::category()]];
		list($query, $args) = $query->build($this->driver, $config);
		$this->assertEquals("SELECT DISTINCT product_code, category FROM @@products", $query);
		$this->assertNull($args);
	}
	
	//SELECT eq
	public function testSelectEqual() {
		$query = new SelectQueryBuilder($this->profile);
		$query->setCondition(Attr::code()->eq('XXX001'));
		list($query, $args) = $query->build($this->driver, []);
		$this->assertRegExpMatch("/^SELECT \* FROM @@products WHERE product_code = #\{([\w]+)\}/", $query, $matches);
		$index = $matches[1];
		$this->assertArrayHasKey($index, $args);
		$this->assertEquals('XXX001', $args[$index]);
	}
	
	public function testSelectNotEqual() {
		$query = new SelectQueryBuilder($this->profile);
		$query->setCondition(Attr::code()->eq('XXX001', false));
		list($query, $args) = $query->build($this->driver, []);
		$this->assertRegExpMatch("/^SELECT \* FROM @@products WHERE product_code <> #\{([\w]+)\}/", $query, $matches);
		$index = $matches[1];
		$this->assertArrayHasKey($index, $args);
		$this->assertEquals('XXX001', $args[$index]);
	}
	
	//SELECT contains
	public function testSelectContains() {
		$query = new SelectQueryBuilder($this->profile);
		$query->setCondition(Attr::code()->contains('GFX'));
		list($query, $args) = $query->build($this->driver, []);
		$this->assertRegExpMatch("/^SELECT \* FROM @@products WHERE product_code LIKE #\{([\w]+)\}/", $query, $matches);
		$index = $matches[1];
		$this->assertArrayHasKey($index, $args);
		$this->assertEquals('%GFX%', $args[$index]);
	}
	
	public function testSelectNotContains() {
		$query = new SelectQueryBuilder($this->profile);
		$query->setCondition(Attr::code()->contains('GFX', false));
		list($query, $args) = $query->build($this->driver, []);
		$this->assertRegExpMatch("/^SELECT \* FROM @@products WHERE product_code NOT LIKE #\{([\w]+)\}/", $query, $matches);
		$index = $matches[1];
		$this->assertArrayHasKey($index, $args);
		$this->assertEquals('%GFX%', $args[$index]);
	}
	
	//SELECT icontains
	public function testSelectIContains() {
		$query = new SelectQueryBuilder($this->profile);
		$query->setCondition(Attr::code()->icontains('GFX'));
		list($query, $args) = $query->build($this->driver, []);
		$this->assertRegExpMatch("/^SELECT \* FROM @@products WHERE product_code ILIKE #\{([\w]+)\}/", $query, $matches);
		$index = $matches[1];
		$this->assertArrayHasKey($index, $args);
		$this->assertEquals('%GFX%', $args[$index]);
	}
	
	public function testSelectNotIContains() {
		$query = new SelectQueryBuilder($this->profile);
		$query->setCondition(Attr::code()->icontains('GFX', false));
		list($query, $args) = $query->build($this->driver, []);
		$this->assertRegExpMatch("/^SELECT \* FROM @@products WHERE product_code NOT ILIKE #\{([\w]+)\}/", $query, $matches);
		$index = $matches[1];
		$this->assertArrayHasKey($index, $args);
		$this->assertEquals('%GFX%', $args[$index]);
	}
	
	//SELECT in
	public function testSelectIn() {
		$query = new SelectQueryBuilder($this->profile);
		$query->setCondition(Attr::id()->in([3, 4]));
		list($query, $args) = $query->build($this->driver, []);
		$this->assertRegExpMatch('/SELECT \* FROM @@products WHERE product_id IN \(#\{([\w]+):int\}\)/', $query, $matches);
		$index = $matches[1];
		$this->assertArrayHasKey($index, $args);
		$this->assertEquals([3, 4], $args[$index]);
	}
	
	public function testSelectNotIn() {
		$query = new SelectQueryBuilder($this->profile);
		$query->setCondition(Attr::id()->in([3, 4], false));
		list($query, $args) = $query->build($this->driver, []);
		$this->assertRegExpMatch('/SELECT \* FROM @@products WHERE product_id NOT IN \(#\{([\w]+):int\}\)/', $query, $matches);
		$index = $matches[1];
		$this->assertArrayHasKey($index, $args);
		$this->assertEquals([3, 4], $args[$index]);
	}
	
	//SELECT gt
	public function testSelectGreaterThan() {
		$query = new SelectQueryBuilder($this->profile);
		$query->setCondition(Attr::id()->gt(3));
		list($query, $args) = $query->build($this->driver, []);
		$this->assertRegExpMatch('/SELECT \* FROM @@products WHERE product_id > #\{([\w]+):int\}/', $query, $matches);
		$index = $matches[1];
		$this->assertArrayHasKey($index, $args);
		$this->assertEquals(3, $args[$index]);
	}
	
	public function testSelectNotGreaterThan() {
		$query = new SelectQueryBuilder($this->profile);
		$query->setCondition(Attr::id()->gt(3, false));
		list($query, $args) = $query->build($this->driver, []);
		$this->assertRegExpMatch('/SELECT \* FROM @@products WHERE product_id <= #\{([\w]+):int\}/', $query, $matches);
		$index = $matches[1];
		$this->assertArrayHasKey($index, $args);
		$this->assertEquals(3, $args[$index]);
	}
	
	//SELECT gte
	public function testSelectGreaterThanEqual() {
		$query = new SelectQueryBuilder($this->profile);
		$query->setCondition(Attr::id()->gte(3));
		list($query, $args) = $query->build($this->driver, []);
		$this->assertRegExpMatch('/SELECT \* FROM @@products WHERE product_id >= #\{([\w]+):int\}/', $query, $matches);
		$index = $matches[1];
		$this->assertArrayHasKey($index, $args);
		$this->assertEquals(3, $args[$index]);
	}
	
	public function testSelectNotGreaterThanEqual() {
		$query = new SelectQueryBuilder($this->profile);
		$query->setCondition(Attr::id()->gte(3, false));
		list($query, $args) = $query->build($this->driver, []);
		$this->assertRegExpMatch('/SELECT \* FROM @@products WHERE product_id < #\{([\w]+):int\}/', $query, $matches);
		$index = $matches[1];
		$this->assertArrayHasKey($index, $args);
		$this->assertEquals(3, $args[$index]);
	}
	
	//SELECT lt
	public function testSelectLessThan() {
		$query = new SelectQueryBuilder($this->profile);
		$query->setCondition(Attr::id()->lt(3));
		list($query, $args) = $query->build($this->driver, []);
		$this->assertRegExpMatch('/SELECT \* FROM @@products WHERE product_id < #\{([\w]+):int\}/', $query, $matches);
		$index = $matches[1];
		$this->assertArrayHasKey($index, $args);
		$this->assertEquals(3, $args[$index]);
	}
	
	public function testSelectNotLessThan() {
		$query = new SelectQueryBuilder($this->profile);
		$query->setCondition(Attr::id()->lt(3, false));
		list($query, $args) = $query->build($this->driver, []);
		$this->assertRegExpMatch('/SELECT \* FROM @@products WHERE product_id >= #\{([\w]+):int\}/', $query, $matches);
		$index = $matches[1];
		$this->assertArrayHasKey($index, $args);
		$this->assertEquals(3, $args[$index]);
	}
	
	//SELECT lte
	public function testSelectLessThanEqual() {
		$query = new SelectQueryBuilder($this->profile);
		$query->setCondition(Attr::id()->lte(3));
		list($query, $args) = $query->build($this->driver, []);
		$this->assertRegExpMatch('/SELECT \* FROM @@products WHERE product_id <= #\{([\w]+):int\}/', $query, $matches);
		$index = $matches[1];
		$this->assertArrayHasKey($index, $args);
		$this->assertEquals(3, $args[$index]);
	}
	
	public function testSelectNotLessThanEqual() {
		$query = new SelectQueryBuilder($this->profile);
		$query->setCondition(Attr::id()->lte(3, false));
		list($query, $args) = $query->build($this->driver, []);
		$this->assertRegExpMatch('/SELECT \* FROM @@products WHERE product_id > #\{([\w]+):int\}/', $query, $matches);
		$index = $matches[1];
		$this->assertArrayHasKey($index, $args);
		$this->assertEquals(3, $args[$index]);
	}
	
	//SELECT startswith
	public function testSelectStartsWith() {
		$query = new SelectQueryBuilder($this->profile);
		$query->setCondition(Attr::code()->startswith('IND'));
		list($query, $args) = $query->build($this->driver, []);
		$this->assertRegExpMatch("/SELECT \* FROM @@products WHERE product_code LIKE #\{([\w]+)\}/", $query, $matches);
		$index = $matches[1];
		$this->assertArrayHasKey($index, $args);
		$this->assertEquals('IND%', $args[$index]);
	}
	
	public function testSelectNotStartsWith() {
		$query = new SelectQueryBuilder($this->profile);
		$query->setCondition(Attr::code()->startswith('IND', false));
		list($query, $args) = $query->build($this->driver, []);
		$this->assertRegExpMatch("/SELECT \* FROM @@products WHERE product_code NOT LIKE #\{([\w]+)\}/", $query, $matches);
		$index = $matches[1];
		$this->assertArrayHasKey($index, $args);
		$this->assertEquals('IND%', $args[$index]);
	}
	
	//SELECT istartswith
	public function testSelectIStartsWith() {
		$query = new SelectQueryBuilder($this->profile);
		$query->setCondition(Attr::code()->istartswith('IND'));
		list($query, $args) = $query->build($this->driver, []);
		$this->assertRegExpMatch("/SELECT \* FROM @@products WHERE product_code ILIKE #\{([\w]+)\}/", $query, $matches);
		$index = $matches[1];
		$this->assertArrayHasKey($index, $args);
		$this->assertEquals('IND%', $args[$index]);
	}
	
	public function testSelectNotIStartsWith() {
		$query = new SelectQueryBuilder($this->profile);
		$query->setCondition(Attr::code()->istartswith('IND', false));
		list($query, $args) = $query->build($this->driver, []);
		$this->assertRegExpMatch("/SELECT \* FROM @@products WHERE product_code NOT ILIKE #\{([\w]+)\}/", $query, $matches);
		$index = $matches[1];
		$this->assertArrayHasKey($index, $args);
		$this->assertEquals('IND%', $args[$index]);
	}
	
	//SELECT endswith
	public function testSelectEndsWith() {
		$query = new SelectQueryBuilder($this->profile);
		$query->setCondition(Attr::code()->endswith('232'));
		list($query, $args) = $query->build($this->driver, []);
		$this->assertRegExpMatch("/SELECT \* FROM @@products WHERE product_code LIKE #\{([\w]+)\}/", $query, $matches);
		$index = $matches[1];
		$this->assertArrayHasKey($index, $args);
		$this->assertEquals('%232', $args[$index]);
	}
	
	public function testSelectNotEndsWith() {
		$query = new SelectQueryBuilder($this->profile);
		$query->setCondition(Attr::code()->endswith('232', false));
		list($query, $args) = $query->build($this->driver, []);
		$this->assertRegExpMatch("/SELECT \* FROM @@products WHERE product_code NOT LIKE #\{([\w]+)\}/", $query, $matches);
		$index = $matches[1];
		$this->assertArrayHasKey($index, $args);
		$this->assertEquals('%232', $args[$index]);
	}
	
	//SELECT iendswith
	public function testSelectIEndsWith() {
		$query = new SelectQueryBuilder($this->profile);
		$query->setCondition(Attr::code()->iendswith('232'));
		list($query, $args) = $query->build($this->driver, []);
		$this->assertRegExpMatch("/SELECT \* FROM @@products WHERE product_code ILIKE #\{([\w]+)\}/", $query, $matches);
		$index = $matches[1];
		$this->assertArrayHasKey($index, $args);
		$this->assertEquals('%232', $args[$index]);
	}
	
	public function testSelectNotIEndsWith() {
		$query = new SelectQueryBuilder($this->profile);
		$query->setCondition(Attr::code()->iendswith('232', false));
		list($query, $args) = $query->build($this->driver, []);
		$this->assertRegExpMatch("/SELECT \* FROM @@products WHERE product_code NOT ILIKE #\{([\w]+)\}/", $query, $matches);
		$index = $matches[1];
		$this->assertArrayHasKey($index, $args);
		$this->assertEquals('%232', $args[$index]);
	}
	
	//SELECT range
	public function testSelectRange() {
		$query = new SelectQueryBuilder($this->profile);
		$query->setCondition(Attr::id()->range(2, 4));
		list($query, $args) = $query->build($this->driver, []);
		$this->assertRegExpMatch("/SELECT \* FROM @@products WHERE product_id BETWEEN #\{([\w]+):int\} AND #\{([\w]+):int\}/", $query, $matches);
		$from_index = $matches[1];
		$to_index = $matches[2];
		$this->assertArrayHasKey($from_index, $args);
		$this->assertArrayHasKey($to_index, $args);
		$this->assertEquals(2, $args[$from_index]);
		$this->assertEquals(4, $args[$to_index]);
	}
	
	public function testSelectNotRange() {
		$query = new SelectQueryBuilder($this->profile);
		$query->setCondition(Attr::id()->range(2, 4, false));
		list($query, $args) = $query->build($this->driver, []);
		$this->assertRegExpMatch("/SELECT \* FROM @@products WHERE product_id NOT BETWEEN #\{([\w]+):int\} AND #\{([\w]+):int\}/", $query, $matches);
		$from_index = $matches[1];
		$to_index = $matches[2];
		$this->assertArrayHasKey($from_index, $args);
		$this->assertArrayHasKey($to_index, $args);
		$this->assertEquals(2, $args[$from_index]);
		$this->assertEquals(4, $args[$to_index]);
	}
	
	//SELECT isnull
	public function testSelectIsNull() {
		$query = new SelectQueryBuilder($this->profile);
		$query->setCondition(Attr::color()->isnull());
		list($query, $args) = $query->build($this->driver, []);
		$this->assertRegExpMatch("/SELECT \* FROM @@products WHERE color IS NULL/", $query, $matches);
		$this->assertEmpty($args);
	}
	
	public function testSelectIsNotNull() {
		$query = new SelectQueryBuilder($this->profile);
		$query->setCondition(Attr::color()->isnull(false));
		list($query, $args) = $query->build($this->driver, []);
		$this->assertRegExpMatch("/SELECT \* FROM @@products WHERE color IS NOT NULL/", $query, $matches);
		$this->assertEmpty($args);
	}
	
	//INSERT
	public function testInsert() {
		$query = new InsertQueryBuilder($this->profile);
		list($query, $args) = $query->build($this->driver);
		$this->assertNull($args);
		$this->assertEquals("INSERT INTO @@products (product_id, product_code, category, color) VALUES (#{id:int}, #{code}, #{category}, #{color:Acme\RGBColor})", $query);
	}
	
	//UPDATE
	public function testUpdate() {
		$query = new UpdateQueryBuilder($this->profile);
		$query->setCondition(Attr::id()->eq(2));
		list($query, $args) = $query->build($this->driver);
		$this->assertRegExpMatch('/UPDATE @@products SET product_id = #\{\w+:int\}, product_code = #\{\w+\}, category = #\{\w+\}, color = #\{\w+:Acme\\\\RGBColor\} WHERE product_id = %\{1\[(\d+):int\]\}/', $query, $matches);
		$this->assertInternalType('array', $args);
		$this->assertCount(1, $args);
		$index = intval($matches[1]);
		$this->assertArrayHasKey($index, $args);
		$this->assertEquals(2, $args[$index]);
	}
	
	//DELETE
	public function testDeleteByPK() {
		$query = new DeleteQueryBuilder($this->profile);
		$query->setCondition(Attr::id()->eq(1));
		list($query, $args) = $query->build($this->driver);
		$this->assertRegExpMatch("/DELETE FROM @@products WHERE product_id = #\{(arg[\d]+)(:[\w]+)?\}/", $query, $matches);
		$this->assertInternalType('array', $args);
		$this->assertCount(1, $args);
		$this->assertCount(3, $matches);
		$key = $matches[1];
		$this->assertArrayHasKey($key, $args);
		$this->assertEquals(1, $args[$key]);
		$this->assertEquals(':int', $matches[2]);
	}
	
	public function testDeleteByColor() {
		$query = new DeleteQueryBuilder($this->profile);
		$query->setCondition(Attr::color('s')->eq(null, false));
		list($query, $args) = $query->build($this->driver);
		$this->assertRegExp("/DELETE FROM @@products WHERE color IS NOT #\{(arg[\d]+):s\}/", $query);
		$this->assertInternalType('array', $args);
		$this->assertContains(null, $args);
	}
	
	public function testDeleteByNullColor() {
		$query = new DeleteQueryBuilder($this->profile);
		$query->setCondition(Attr::color()->isnull());
		list($query, $args) = $query->build($this->driver);
		$this->assertEquals("DELETE FROM @@products WHERE color IS NULL", $query);
		$this->assertInternalType('array', $args);
		$this->assertCount(0, $args);
	}
	
	public function testDeleteByFilter() {
		$query = new DeleteQueryBuilder($this->profile);
		$query->setCondition(Q::filter(Attr::category()->eq('Clothes'), Column::year()->lt(2012)));
		list($query, $args) = $query->build($this->driver);
		$this->assertRegExpMatch("/DELETE FROM @@products WHERE \( category = #\{(arg[\d]+)\} AND year < #\{(arg[\d]+)\}\ \)/", $query, $matches);
		$category_key = $matches[1];
		$year_key = $matches[2];
		$this->assertArrayHasKey($category_key, $args);
		$this->assertArrayHasKey($year_key, $args);
		$this->assertEquals('Clothes', $args[$category_key]);
		$this->assertEquals(2012, $args[$year_key]);
	}
	
	public function testDeleteByWhere() {
		$query = new DeleteQueryBuilder($this->profile);
		$query->setCondition(Q::where(Attr::category()->eq('Clothes', false), Column::year()->gte(2012)));
		list($query, $args) = $query->build($this->driver);
		$this->assertRegExpMatch("/DELETE FROM @@products WHERE \( category <> #\{(arg[\d]+)\} OR year >= #\{(arg[\d]+)\}\ \)/", $query, $matches);
		$category_key = $matches[1];
		$year_key = $matches[2];
		$this->assertArrayHasKey($category_key, $args);
		$this->assertArrayHasKey($year_key, $args);
		$this->assertEquals('Clothes', $args[$category_key]);
		$this->assertEquals(2012, $args[$year_key]);
	}
	
	public function testDeleteByWhereNot() {
		$query = new DeleteQueryBuilder($this->profile);
		$query->setCondition(Q::where_not(Attr::category()->eq('Clothes', false), Column::year()->gte(2012)));
		list($query, $args) = $query->build($this->driver);
		$this->assertRegExpMatch("/DELETE FROM @@products WHERE NOT \( category <> #\{(arg[\d]+)\} OR year >= #\{(arg[\d]+)\}\ \)/", $query, $matches);
		$category_key = $matches[1];
		$year_key = $matches[2];
		$this->assertArrayHasKey($category_key, $args);
		$this->assertArrayHasKey($year_key, $args);
		$this->assertEquals('Clothes', $args[$category_key]);
		$this->assertEquals(2012, $args[$year_key]);
	}
	
	public function testDeleteByConfig() {
		$query = new DeleteQueryBuilder($this->profile);
		$config = ['query.filter' => [Attr::code()->eq('XXX001')]];
		list($query, $args) = $query->build($this->driver, $config);
		$this->assertRegExpMatch("/DELETE FROM @@products WHERE product_code = #\{(arg[\d]+)\}/", $query, $matches);
		$code_key = $matches[1];
		$this->assertArrayHasKey($code_key, $args);
		$this->assertEquals('XXX001', $args[$code_key]);
	}
	
	public function testDeleteByFilterConfig() {
		$query = new DeleteQueryBuilder($this->profile);
		$config = ['query.filter' => [Attr::code()->eq('XXX001', false), Column::year()->lt(2012)]];
		list($query, $args) = $query->build($this->driver, $config);
		$this->assertRegExpMatch("/DELETE FROM @@products WHERE \( product_code <> #\{(arg[\d]+)\} AND year < #\{(arg[\d]+)\} \)/", $query, $matches);
		$code_key = $matches[1];
		$year_key = $matches[2];
		$this->assertArrayHasKey($code_key, $args);
		$this->assertArrayHasKey($year_key, $args);
		$this->assertEquals('XXX001', $args[$code_key]);
		$this->assertEquals(2012, $args[$year_key]);
	}
	
	public function testTruncate() {
		$query = new DeleteQueryBuilder($this->profile, true);
		list($query, $args) = $query->build($this->driver);
		$this->assertEquals("DELETE FROM @@products", $query);
		$this->assertNull($args);
	}
}
?>