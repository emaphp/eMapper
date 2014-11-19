<?php
namespace eMapper;

abstract class MapperTest extends \PHPUnit_Framework_TestCase {
	/**
	 * Database mapper
	 * @var Mapper
	 */
	protected $mapper;
	
	abstract function getMapper();
	
	public function setUp() {
		$this->mapper = $this->getMapper();
	}
	
	public function tearDown() {
		if ($this->mapper instanceof Mapper)
			$this->mapper->close();
	}
}
?>