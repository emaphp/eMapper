<?php
namespace eMapper\Mapper;

abstract class AbstractMapperTest extends \PHPUnit_Framework_TestCase {
	protected $mapper;
	
	public function setUp() {
		$this->mapper = $this->getMapper();
	}
	
	public function tearDown() {
		$this->mapper->close();
	}
}
?>