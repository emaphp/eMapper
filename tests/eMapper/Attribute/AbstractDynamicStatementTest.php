<?php
namespace eMapper\Attribute;

use eMapper\MapperTest;
use eMapper\Engine\MySQL\MySQLDriver;

abstract class AbstractDynamicStatementTest extends MapperTest {
	public function testfindByPk() {
		$manager = $this->mapper->newManager('Acme\Statement\Category');
		$category = $manager->findByPk(6);
		$this->assertInstanceOf('Acme\Statement\Category', $category->parent);
		$this->assertEquals(2, $category->parent->id);
	}
	
	public function testfindBy() {
		$manager = $this->mapper->newManager('Acme\Statement\Category');
		$category = $manager->findByPk(6);
		$this->assertInternalType('array', $category->subcategories);
		$this->assertCount(3, $category->subcategories);
	}
	
	public function testFindAll() {
		$manager = $this->mapper->newManager('Acme\Statement\Product');
		$product = $manager->findByPk(1);
		$this->assertInternalType('array', $product->categories);
		$this->assertCount(16, $product->categories);
	}
	
	public function testFindByUnique() {
		$manager = $this->mapper->newManager('Acme\Statement\Profile');
		$profile = $manager->findByPk(1);
		$this->assertInstanceOf('Acme\Statement\User', $profile->user);
		$this->assertEquals('jdoe', $profile->user->name);
	}
	
	public function testEqualsUnique() {
		$manager = $this->mapper->newManager('Acme\Statement\Sale');
		$sale = $manager->findByPk(1);
		$this->assertInstanceOf('Acme\Statement\Product', $sale->product);
		$this->assertEquals('PHN00098', $sale->product->code);
	}
	
	public function testEquals() {
		$manager = $this->mapper->newManager('Acme\Statement\Product');
		$product = $manager->findByPk(2);
		$this->assertInternalType('array', $product->sales);
		$this->assertCount(1, $product->sales);
	}
	
	public function testNotEquals() {
		$manager = $this->mapper->newManager('Acme\Statement\Product');
		$product = $manager->findByPk(1);
		$this->assertInternalType('array', $product->notSales);
		$this->assertCount(4, $product->notSales);
	}
	
	public function testNotEqualsUnique() {
		$manager = $this->mapper->newManager('Acme\Statement\Sale');
		$sale = $manager->findByPk(1);
		$this->assertInternalType('array', $sale->otherProducts);
		$this->assertCount(7, $sale->otherProducts);
	}
	
	/*
	 * CONTAINS
	 */
	
	public function testContains() {
		$sale = $this->mapper
		->resultMap('Acme\Statement\ContainsResultMap')
		->type('obj')
		->query("SELECT sale_id FROM sales WHERE sale_id = %{i}", 1);
		
		$this->assertEquals(1, $sale->saleId);
		
		$this->assertInternalType('array', $sale->contains);
		$this->assertCount(1, $sale->contains);
	}
	
	public function testNotContains() {
		$sale = $this->mapper
		->resultMap('Acme\Statement\ContainsResultMap')
		->type('obj')
		->query("SELECT sale_id FROM sales WHERE sale_id = %{i}", 1);
	
		$this->assertEquals(1, $sale->saleId);
	
		$this->assertInternalType('array', $sale->notContains);
		$this->assertCount(5, $sale->notContains);
	}
	
	public function testIContains() {
		$sale = $this->mapper
		->resultMap('Acme\Statement\ContainsResultMap')
		->type('obj')
		->query("SELECT sale_id FROM sales WHERE sale_id = %{i}", 1);
		
		$this->assertEquals(1, $sale->saleId);
		$this->assertInternalType('array', $sale->icontains);
		$this->assertCount(3, $sale->icontains);
	}
	
	public function testNotIContains() {
		$sale = $this->mapper
		->resultMap('Acme\Statement\ContainsResultMap')
		->type('obj')
		->query("SELECT sale_id FROM sales WHERE sale_id = %{i}", 1);
	
		$this->assertEquals(1, $sale->saleId);
		$this->assertInternalType('array', $sale->notIContains);
		$this->assertCount(5, $sale->notIContains);
	}
	
	/*
	 * STARTSWITH 
	 */
	
	public function testStartsWith() {
		$sale = $this->mapper
		->resultMap('Acme\Statement\StartsWithResultMap')
		->type('obj')
		->query("SELECT sale_id FROM sales WHERE sale_id = %{i}", 1);
		
		$this->assertInternalType('array', $sale->startsWith);
		$this->assertCount(3, $sale->startsWith);
		$this->assertInstanceOf('Acme\Statement\Product', $sale->startsWith[0]);
	}
	
	public function testNotStartsWith() {
		$sale = $this->mapper
		->resultMap('Acme\Statement\StartsWithResultMap')
		->type('obj')
		->query("SELECT sale_id FROM sales WHERE sale_id = %{i}", 1);
	
		$this->assertInternalType('array', $sale->notStartsWith);
		$this->assertCount(5, $sale->notStartsWith);
		$this->assertInstanceOf('Acme\Statement\Product', $sale->notStartsWith[0]);
	}
	
	public function testIStartsWith() {
		$sale = $this->mapper
		->resultMap('Acme\Statement\StartsWithResultMap')
		->type('obj')
		->query("SELECT sale_id FROM sales WHERE sale_id = %{i}", 1);
	
		$this->assertInternalType('array', $sale->istartsWith);
		$this->assertCount(1, $sale->istartsWith);
		$this->assertInstanceOf('Acme\Statement\Product', $sale->istartsWith[0]);
	}
	
	public function testNotIStartsWith() {
		$sale = $this->mapper
		->resultMap('Acme\Statement\StartsWithResultMap')
		->type('obj')
		->query("SELECT sale_id FROM sales WHERE sale_id = %{i}", 1);
	
		$this->assertInternalType('array', $sale->notIStartsWith);
		$this->assertCount(7, $sale->notIStartsWith);
		$this->assertInstanceOf('Acme\Statement\Product', $sale->notIStartsWith[0]);
	}
	
	/*
	 * ENDSWITH
	 */
	
	public function testEndsWith() {
		$sale = $this->mapper
		->resultMap('Acme\Statement\EndsWithResultMap')
		->type('obj')
		->query("SELECT sale_id FROM sales WHERE sale_id = %{i}", 1);
	
		$this->assertInternalType('array', $sale->endsWith);
		$this->assertCount(2, $sale->endsWith);
		$this->assertInstanceOf('Acme\Statement\Product', $sale->endsWith[0]);
	}
	
	public function testNotEndsWith() {
		$sale = $this->mapper
		->resultMap('Acme\Statement\EndsWithResultMap')
		->type('obj')
		->query("SELECT sale_id FROM sales WHERE sale_id = %{i}", 1);
	
		$this->assertInternalType('array', $sale->notEndsWith);
		$this->assertCount(3, $sale->notEndsWith);
		$this->assertInstanceOf('Acme\Statement\Product', $sale->notEndsWith[0]);
	}
	
	public function testIEndsWith() {
		$sale = $this->mapper
		->resultMap('Acme\Statement\EndsWithResultMap')
		->type('obj')
		->query("SELECT sale_id FROM sales WHERE sale_id = %{i}", 1);
	
		$this->assertInternalType('array', $sale->iendsWith);
		$this->assertCount(2, $sale->iendsWith);
		$this->assertInstanceOf('Acme\Statement\Product', $sale->iendsWith[0]);
	}
	
	public function testNotIEndsWith() {
		$sale = $this->mapper
		->resultMap('Acme\Statement\EndsWithResultMap')
		->type('obj')
		->query("SELECT sale_id FROM sales WHERE sale_id = %{i}", 1);
	
		$this->assertInternalType('array', $sale->notIEndsWith);
		$this->assertCount(6, $sale->notIEndsWith);
		$this->assertInstanceOf('Acme\Statement\Product', $sale->notIEndsWith[0]);
	}
	
	/*
	 * ISNULL
	 */
	
	public function testIsNull() {
		$sale = $this->mapper
		->resultMap('Acme\Statement\IsNullResultMap')
		->type('obj')
		->query("SELECT sale_id FROM sales WHERE sale_id = %{i}", 1);
		
		$this->assertInternalType('array', $sale->isNull);
		$this->assertCount(3, $sale->isNull);
		$this->assertInstanceOf('Acme\Statement\Product', $sale->isNull[0]);
	}
	
	public function testIsNotNull() {
		$sale = $this->mapper
		->resultMap('Acme\Statement\IsNullResultMap')
		->type('obj')
		->query("SELECT sale_id FROM sales WHERE sale_id = %{i}", 1);
		
		$this->assertInternalType('array', $sale->isNotNull);
		$this->assertCount(5, $sale->isNotNull);
		$this->assertInstanceOf('Acme\Statement\Product', $sale->isNotNull[0]);
	}
	
	/*
	 * GREATERTHAN
	 */
	
	public function testGreaterThan() {
		$sale = $this->mapper
		->resultMap('Acme\Statement\GreaterThanResultMap')
		->type('obj')
		->query("SELECT sale_id FROM sales WHERE sale_id = %{i}", 1);
		
		$this->assertInternalType('array', $sale->greaterThan);
		$this->assertCount(4, $sale->greaterThan);
		$this->assertInstanceOf('Acme\Statement\Product', $sale->greaterThan[0]);
	}
	
	public function testNotGreaterThan() {
		$sale = $this->mapper
		->resultMap('Acme\Statement\GreaterThanResultMap')
		->type('obj')
		->query("SELECT sale_id FROM sales WHERE sale_id = %{i}", 1);
		
		$this->assertInternalType('array', $sale->notGreaterThan);
		$this->assertCount(3, $sale->notGreaterThan);
		$this->assertInstanceOf('Acme\Statement\Product', $sale->notGreaterThan[0]);
	}
	
	public function testGreaterThanEqual() {
		$sale = $this->mapper
		->resultMap('Acme\Statement\GreaterThanResultMap')
		->type('obj')
		->query("SELECT sale_id FROM sales WHERE sale_id = %{i}", 1);
		
		$this->assertInternalType('array', $sale->greaterThanEqual);
		$this->assertCount(1, $sale->greaterThanEqual);
		$this->assertInstanceOf('Acme\Statement\Product', $sale->greaterThanEqual[0]);
	}
	
	public function testNotGreaterThanEqual() {
		$sale = $this->mapper
		->resultMap('Acme\Statement\GreaterThanResultMap')
		->type('obj')
		->query("SELECT sale_id FROM sales WHERE sale_id = %{i}", 1);
		
		$this->assertInternalType('array', $sale->notGreaterThanEqual);
		//KNOWN ISSUE: MYSQL + FLOAT type column (DECIMAL does not have this behaviour)
		$this->assertCount($this->mapper->getDriver() instanceof MySQLDriver ? 3 : 2, $sale->notGreaterThanEqual);
		$this->assertInstanceOf('Acme\Statement\Product', $sale->notGreaterThanEqual[0]);
	}
	
	/*
	 * LESSTHAN
	 */
	
	public function testLessThan() {
		$sale = $this->mapper
		->resultMap('Acme\Statement\LessThanResultMap')
		->type('obj')
		->query("SELECT sale_id FROM sales WHERE sale_id = %{i}", 1);
		
		$this->assertInternalType('array', $sale->lessThan);
		//KNOWN ISSUE: MYSQL + FLOAT type column (DECIMAL does not have this behaviour)
		$this->assertCount($this->mapper->getDriver() instanceof MySQLDriver ? 3 : 2, $sale->lessThan);
		$this->assertInstanceOf('Acme\Statement\Product', $sale->lessThan[0]);
	}
	
	public function testNotLessThan() {
		$sale = $this->mapper
		->resultMap('Acme\Statement\LessThanResultMap')
		->type('obj')
		->query("SELECT sale_id FROM sales WHERE sale_id = %{i}", 1);
		
		$this->assertInternalType('array', $sale->notLessThan);
		$this->assertCount(1, $sale->notLessThan);
		$this->assertInstanceOf('Acme\Statement\Product', $sale->notLessThan[0]);
	}
	
	public function testLessThanEqual() {
		$sale = $this->mapper
		->resultMap('Acme\Statement\LessThanResultMap')
		->type('obj')
		->query("SELECT sale_id FROM sales WHERE sale_id = %{i}", 1);
		
		$this->assertInternalType('array', $sale->lessThanEqual);
		$this->assertCount(3, $sale->lessThanEqual);
		$this->assertInstanceOf('Acme\Statement\Product', $sale->lessThanEqual[0]);
	}
	
	public function testNotLessThanEqual() {
		$sale = $this->mapper
		->resultMap('Acme\Statement\LessThanResultMap')
		->type('obj')
		->query("SELECT sale_id FROM sales WHERE sale_id = %{i}", 1);
		
		$this->assertInternalType('array', $sale->notLessThanEqual);
		$this->assertCount(4, $sale->notLessThanEqual);
		$this->assertInstanceOf('Acme\Statement\Product', $sale->notLessThanEqual[0]);
	}
	
	/*
	 * BETWEEN
	 */
	
	public function testBetween() {
		$sale = $this->mapper
		->resultMap('Acme\Statement\BetweenResultMap')
		->type('obj')
		->query("SELECT sale_id FROM sales WHERE sale_id = %{i}", 1);
		
		$this->assertInternalType('array', $sale->between);
		$this->assertCount(3, $sale->between);
		$this->assertInstanceOf('Acme\Statement\Product', $sale->between[0]);
	}
	
	public function testNotBetween() {
		$sale = $this->mapper
		->resultMap('Acme\Statement\BetweenResultMap')
		->type('obj')
		->query("SELECT sale_id FROM sales WHERE sale_id = %{i}", 1);
	
		$this->assertInternalType('array', $sale->notBetween);
		$this->assertCount(5, $sale->notBetween);
		$this->assertInstanceOf('Acme\Statement\Product', $sale->notBetween[0]);
	}
	
	/*
	 * MATCHES
	 */
	
	public function testMatches() {
		$sale = $this->mapper
		->resultMap('Acme\Statement\MatchesResultMap')
		->type('obj')
		->query("SELECT sale_id FROM sales WHERE sale_id = %{i}", 1);
	
		$this->assertInternalType('array', $sale->matches);
		$this->assertCount(3, $sale->matches);
		$this->assertInstanceOf('Acme\Statement\Product', $sale->matches[0]);
	}
	
	public function testNotMatches() {
		$sale = $this->mapper
		->resultMap('Acme\Statement\MatchesResultMap')
		->type('obj')
		->query("SELECT sale_id FROM sales WHERE sale_id = %{i}", 1);
	
		$this->assertInternalType('array', $sale->notMatches);
		$this->assertCount(5, $sale->notMatches);
		$this->assertInstanceOf('Acme\Statement\Product', $sale->notMatches[0]);
	}
	
	public function testIMatches() {
		$sale = $this->mapper
		->resultMap('Acme\Statement\MatchesResultMap')
		->type('obj')
		->query("SELECT sale_id FROM sales WHERE sale_id = %{i}", 1);
	
		$this->assertInternalType('array', $sale->imatches);
		$this->assertCount(2, $sale->imatches);
		$this->assertInstanceOf('Acme\Statement\Product', $sale->imatches[0]);
	}
	
	public function testNotIMatches() {
		$sale = $this->mapper
		->resultMap('Acme\Statement\MatchesResultMap')
		->type('obj')
		->query("SELECT sale_id FROM sales WHERE sale_id = %{i}", 1);
	
		$this->assertInternalType('array', $sale->notIMatches);
		$this->assertCount(6, $sale->notIMatches);
		$this->assertInstanceOf('Acme\Statement\Product', $sale->notIMatches[0]);
	}
}