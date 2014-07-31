<?php
namespace eMapper\SQL;

use eMapper\Reflection\Profiler;
use eMapper\Reflection\Profile\ClassProfile;
use eMapper\Engine\Generic\Driver;
use eMapper\Reflection\EntityMapper;
use eMapper\SQL\Builder\FindAllStatemetBuilder;
use eMapper\SQL\Builder\FindByPkStatementBuilder;
use eMapper\SQL\Builder\FindByStatementBuilder;
use eMapper\SQL\Builder\EqualStatementBuilder;
use eMapper\SQL\Builder\ContainStatementBuilder;
use eMapper\SQL\Builder\InStatementBuilder;
use eMapper\SQL\Builder\GreaterThanStatementBuilder;
use eMapper\SQL\Builder\LessThanStatementBuilder;
use eMapper\SQL\Builder\StartsWithStatementBuilder;
use eMapper\SQL\Builder\EndsWithStatementBuilder;
use eMapper\SQL\Builder\IsNullStatementBuilder;
use eMapper\SQL\Builder\RegexStatementBuilder;
use eMapper\SQL\Builder\RangeStatementBuilder;
use eMapper\SQL\Configuration\StatementConfigurationContainer;

/**
 * The EntityNamespace class represents a SQLNamespace generated from an entity class.
 * @author emaphp
 */
class EntityNamespace extends SQLNamespace {
	use EntityMapper;
	
	/**
	 * Entity profile
	 * @var ClassProfile
	 */
	protected $entity;
	
	/**
	 * Connection driver
	 * @var Driver
	 */
	protected $driver;
	
	/**
	 * Default mapping type expression
	 * @var StatementConfigurationContainer
	 */
	protected $statementReturnType;
	
	/**
	 * Default mapping type expression (list)
	 * @var StatementConfigurationContainer
	 */
	protected $statementReturnTypeList;
	
	public function __construct($classname) {
		//get class profile
		$this->entity = Profiler::getClassProfile($classname);
		
		//check that this class is a valid entity
		if (!$this->entity->isEntity()) {
			throw new \InvalidArgumentException();
		}
		
		//obtain namespace id and validate
		$namespaceId = $this->entity->getNamespace();
		$this->validateNamespaceId($namespaceId);
		$this->id = $namespaceId;
		
		//generate default mapping type
		$this->statementReturnType = Statement::type($this->buildExpression($this->entity));
		$this->statementReturnTypeList = Statement::type($this->buildListExpression($this->entity));
	}
	
	public function setDriver(Driver $driver) {
		$this->driver = $driver;
	}
	
	/**
	 * Builds an Statement instance and stores it
	 * @param string $id
	 * @param string $query
	 * @param boolean $as_list
	 * @return \eMapper\SQL\Statement
	 */
	protected function buildAndStore($id, $query, $as_list = true) {
		$stmt = new Statement($id, $query, $as_list ? $this->statementReturnTypeList : $this->statementReturnType);
		$this->addStatement($stmt);
		return $stmt;
	}
	
	public function getStatement($statementId) {
		//if it is available then return
		if ($this->hasStatement($statementId)) {
			return parent::getStatement($statementId);
		}
		
		//find all
		if ($statementId == 'findAll') {
			//FindAllStatementBuilder
			$stmt = new FindAllStatemetBuilder($this->driver, $this->entity);
			return $this->buildAndStore('findAll', $stmt->build());
		}
		
		//find by primary key
		if ($statementId == 'findByPk') {
			//FindByPkStatementBuilder
			$stmt = new FindByPkStatementBuilder($this->driver, $this->entity);
			return $this->buildAndStore('finsByPk', $stmt->build(), false);
		}
		
		//find by
		if (preg_match('/^findBy([\w]+)/', $statementId, $matches)) {
			$stmt = new FindByStatementBuilder($this->driver, $this->entity);
			$property = strtolower($matches[1]);
			return $this->buildAndStore($matches[0], $stmt->build($matches), !($this->entity->propertiesConfig[$property]->isUnique || $this->entity->propertiesConfig[$property]->isPrimaryKey));
		}
		
		//eq [FIELD][Not]Equals
		if (preg_match('/^(\w+?)(Not)?Equals$/', $statementId, $matches)) {
			$stmt = new EqualStatementBuilder($this->driver, $this->entity);
			$property = strtolower($matches[1]);
			
			if ($this->entity->propertiesConfig[$property]->isUnique || $this->entity->propertiesConfig[$property]->isPrimaryKey) {
				$as_table = array_key_exists(2, $matches);
			}
			else {
				$as_table = true;
			}
			
			return $this->buildAndStore($matches[0], $stmt->build($matches), $as_table);
		}
		
		//contains/icontains [FIELD][Not][I]Contains
		if (preg_match('/^(\w+?)(Not)?(I)?Contains$/', $statementId, $matches)) {
			$stmt = new ContainStatementBuilder($this->driver, $this->entity);
			return $this->buildAndStore($matches[0], $stmt->build($matches));
		}
		
		//in [FIELD][Not]In
		if (preg_match('/^(\w+?)(Not)?In$/', $statementId, $matches)) {
			$stmt = new InStatementBuilder($this->driver, $this->entity);
			return $this->buildAndStore($matches[0], $stmt->build($matches));
		}
		
		//greater than (equal) [FIELD][Not]GreaterThan[Equal]
		if (preg_match('/^(\w+?)(Not)?GreaterThan(Equal)?$/', $statementId, $matches)) {
			$stmt = new GreaterThanStatementBuilder($this->driver, $this->entity);
			return $this->buildAndStore($matches[0], $stmt->build($matches));
		}
		
		//less than (equal) [FIELD][Not]LessThan[Equal]
		if (preg_match('/^(\w+?)(Not)?LessThan(Equal)?$/', $statementId, $matches)) {
			$stmt = new LessThanStatementBuilder($this->driver, $this->entity);
			return $this->buildAndStore($matches[0], $stmt->build($matches));
		}
		
		//starts with [FIELD][Not][I]StartsWith
		if (preg_match('/^(\w+?)(Not)?(I)?StartsWith$/', $statementId, $matches)) {
			$stmt = new StartsWithStatementBuilder($this->driver, $this->entity);
			return $this->buildAndStore($matches[0], $stmt->build($matches));
		}
		
		//ends with [FIELD][Not][I]EndsWith
		if (preg_match('/^(\w+?)(Not)?(I)?EndsWith$/', $statementId, $matches)) {
			$stmt = new EndsWithStatementBuilder($this->driver, $this->entity);
			return $this->buildAndStore($matches[0], $stmt->build($matches));
		}
		
		//is null [FIELD][Not]IsNull
		if (preg_match('/^(\w+?)Is(Not)?Null$/', $statementId, $matches)) {
			$stmt = new IsNullStatementBuilder($this->driver, $this->entity);
			return $this->buildAndStore($matches[0], $stmt->build($matches));
		}
		
		//matches [FIELD][Not]Matches
		if (preg_match('/^(\w+?)(Not)?(I)?Matches$/', $statementId, $matches)) {
			$stmt = new RegexStatementBuilder($this->driver, $this->entity);
			return $this->buildAndStore($matches[0], $stmt->build($matches));
		}
		
		//range [FIELD][Not]Between
		if (preg_match('/^(\w+?)(Not)?Between$/', $statementId, $matches)) {
			$stmt = new RangeStatementBuilder($this->driver, $this->entity);
			return $this->buildAndStore($matches[0], $stmt->build($matches));
		}
		
		return false;
	}
}
?>