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
	
	//INSERT
	public function testInsert() {
		$query = new InsertQueryBuilder($this->profile);
		list($query, $args) = $query->build($this->driver);
		$this->assertNull($args);
		$this->assertEquals("INSERT INTO products (product_id, product_code, category, color) VALUES (#{id}, #{code}, #{category}, #{color:Acme\RGBColor})", $query);
	}
	
	//UPDATE
	public function testUpdate() {
		$query = new UpdateQueryBuilder($this->profile);
		$query->setCondition(Attr::id()->eq(2));
		list($query, $args) = $query->build($this->driver);
		$this->assertRegExp('/UPDATE products SET product_id = #\{\w+\}, product_code = #\{\w+\}, category = #\{\w+\}, color = #\{\w+:Acme\\\\RGBColor\} WHERE product_id = %\{1\[(\d+)\]\}/', $query);
		$this->assertInternalType('array', $args);
		$this->assertCount(1, $args);
		
		preg_match('/UPDATE products SET product_id = #\{\w+\}, product_code = #\{\w+\}, category = #\{\w+\}, color = #\{\w+:Acme\\\\RGBColor\} WHERE product_id = %\{1\[(\d+)\]\}/', $query, $matches);
		$index = intval($matches[1]);
		$this->assertArrayHasKey($index, $args);
		$this->assertEquals(2, $args[$index]);
	}
	
	//DELETE
	public function testDeleteByPK() {
		$query = new DeleteQueryBuilder($this->profile);
		$query->setCondition(Attr::id()->eq(1));
		list($query, $args) = $query->build($this->driver);
		$this->assertRegExp("/DELETE FROM products WHERE product_id = #\{(arg[\d]+)\}/", $query);
		$this->assertInternalType('array', $args);
		$this->assertCount(1, $args);
		$this->assertContains(1, $args);
		
		preg_match("/DELETE FROM products WHERE product_id = #\{(arg[\d]+)\}/", $query, $matches);
		$key = $matches[1];
		$this->assertArrayHasKey($key, $args);
	}
	
	public function testDeleteByColor() {
		$query = new DeleteQueryBuilder($this->profile);
		$query->setCondition(Attr::color('s')->eq(null, false));
		list($query, $args) = $query->build($this->driver);
		$this->assertRegExp("/DELETE FROM products WHERE color IS NOT #\{(arg[\d]+):s\}/", $query);
		$this->assertInternalType('array', $args);
		$this->assertContains(null, $args);
	}
	
	public function testDeleteByNullColor() {
		$query = new DeleteQueryBuilder($this->profile);
		$query->setCondition(Attr::color()->isnull());
		list($query, $args) = $query->build($this->driver);
		$this->assertEquals("DELETE FROM products WHERE color IS NULL", $query);
		$this->assertInternalType('array', $args);
		$this->assertCount(0, $args);
	}
	
	public function testDeleteByFilter() {
		$query = new DeleteQueryBuilder($this->profile);
		$query->setCondition(Q::filter(Attr::category()->eq('Clothes'), Column::year()->lt(2012)));
		list($query, $args) = $query->build($this->driver);
		$this->assertRegExp("/DELETE FROM products WHERE \( category = #\{(arg[\d]+)\} AND year < #\{(arg[\d]+)\}\ \)/", $query);
		
		preg_match("/DELETE FROM products WHERE \( category = #\{(arg[\d]+)\} AND year < #\{(arg[\d]+)\}\ \)/", $query, $matches);
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
		$this->assertRegExp("/DELETE FROM products WHERE \( category <> #\{(arg[\d]+)\} OR year >= #\{(arg[\d]+)\}\ \)/", $query);
	
		preg_match("/DELETE FROM products WHERE \( category <> #\{(arg[\d]+)\} OR year >= #\{(arg[\d]+)\}\ \)/", $query, $matches);
		$category_key = $matches[1];
		$year_key = $matches[2];
		$this->assertArrayHasKey($category_key, $args);
		$this->assertArrayHasKey($year_key, $args);
		$this->assertEquals('Clothes', $args[$category_key]);
		$this->assertEquals(2012, $args[$year_key]);
	}
	
	public function testDeleteByWhereNOT() {
		$query = new DeleteQueryBuilder($this->profile);
		$query->setCondition(Q::where_not(Attr::category()->eq('Clothes', false), Column::year()->gte(2012)));
		list($query, $args) = $query->build($this->driver);
		$this->assertRegExp("/DELETE FROM products WHERE NOT \( category <> #\{(arg[\d]+)\} OR year >= #\{(arg[\d]+)\}\ \)/", $query);
	
		preg_match("/DELETE FROM products WHERE NOT \( category <> #\{(arg[\d]+)\} OR year >= #\{(arg[\d]+)\}\ \)/", $query, $matches);
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
		$this->assertRegExp("/DELETE FROM products WHERE product_code = #\{(arg[\d]+)\}/", $query);
		preg_match("/DELETE FROM products WHERE product_code = #\{(arg[\d]+)\}/", $query, $matches);
		$code_key = $matches[1];
		$this->assertArrayHasKey($code_key, $args);
		$this->assertEquals('XXX001', $args[$code_key]);
	}
	
	public function testDeleteByFilterConfig() {
		$query = new DeleteQueryBuilder($this->profile);
		$config = ['query.filter' => [Attr::code()->eq('XXX001', false), Column::year()->lt(2012)]];
		list($query, $args) = $query->build($this->driver, $config);
		$this->assertRegExp("/DELETE FROM products WHERE \( product_code <> #\{(arg[\d]+)\} AND year < #\{(arg[\d]+)\} \)/", $query);
		preg_match("/DELETE FROM products WHERE \( product_code <> #\{(arg[\d]+)\} AND year < #\{(arg[\d]+)\} \)/", $query, $matches);
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
		$this->assertEquals("DELETE FROM products", $query);
		$this->assertNull($args);
	}
}
?>