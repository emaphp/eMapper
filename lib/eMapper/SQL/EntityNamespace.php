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
	}
	
	public function setDriver(Driver $driver) {
		$this->driver = $driver;
	}
	
	public function getStatement($statementId) {
		//if it is available then return
		if ($this->hasStatement($statementId)) {
			return parent::getStatement($statementId);
		}
		
		//get table name
		$table = '@@' . $this->entity->getReferredTable();
		
		//find all
		if ($statementId == 'findAll') {
			//FindAllStatementBuilder
			$stmt = new FindAllStatemetBuilder($this->driver, $this->entity);
			return $this->stmt('findAll', $stmt->build(), Statement::type($this->buildListExpression($this->entity)));
		}
		
		//find by primary key
		if ($statementId == 'findByPk') {
			//FindByPkStatementBuilder
			$stmt = new FindByPkStatementBuilder($this->driver, $this->entity);
			return $this->stmt('findByPk', $stmt->build(), Statement::type($this->buildExpression($this->entity)));
		}
		
		//find by
		if (preg_match('/^findBy([\w]+)/', $statementId, $matches)) {
			$stmt = new FindByStatementBuilder($this->driver, $this->entity);
			
			//check if the property is declared as unique
			if ($this->entity->propertiesConfig[$property]->isUnique || $this->entity->propertiesConfig[$property]->isPrimaryKey) {
				$config = Statement::type($this->buildExpression($this->entity));
			}
			else {
				$config = Statement::type($this->buildListExpression($this->entity));
			}
			
			return $this->stmt($matches[0], $stmt->build($matches), $config);
		}
		
		//eq [FIELD][Not]Equals
		if (preg_match('/^(\w+?)(Not)?Equals$/', $statementId, $matches)) {
			$stmt = new EqualStatementBuilder($this->driver, $this->entity);
			
			//check if the property is declared as unique
			if ($this->entity->propertiesConfig[$property]->isUnique || $this->entity->propertiesConfig[$property]->isPrimaryKey) {
				$config = Statement::type($this->buildExpression($this->entity));
			}
			else {
				$config = Statement::type($this->buildListExpression($this->entity));
			}
			
			return $this->stmt($matches[0], $stmt->build($matches), $config);
		}
		
		//contains/icontains [FIELD][Not][I]Contains
		if (preg_match('/^(\w+?)(Not)?(I)?Contains$/', $statementId, $matches)) {
			$stmt = new ContainStatementBuilder($this->driver, $this->entity);
			return $this->stmt($matches[0], $stmt->build($matches), Statement::type($this->buildListExpression($this->entity)));
		}
		
		//in [FIELD][Not]In
		if (preg_match('/^(\w+?)(Not)?In$/', $statementId, $matches)) {
			$stmt = new InStatementBuilder($this->driver, $this->entity);
			return $this->stmt($matches[0], $stmt->build($matches), Statement::type($this->buildListExpression($this->entity)));
		}
		
		//greater than (equal) [FIELD][Not]GreaterThan[Equal]
		if (preg_match('/^(\w+?)(Not)?GreaterThan(Equal)?$/', $statementId, $matches)) {
			$stmt = new GreaterThanStatementBuilder($this->driver, $this->entity);
			return $this->stmt($matches[0], $stmt->build($matches), Statement::type($this->buildListExpression($this->entity)));
		}
		
		//less than (equal) [FIELD][Not]LessThan[Equal]
		if (preg_match('/^(\w+?)(Not)?LessThan(Equal)?$/', $statementId, $matches)) {
			$stmt = new LessThanStatementBuilder($this->driver, $this->entity);
			return $this->stmt($matches[0], $stmt->build($matches), Statement::type($this->buildListExpression($this->entity)));
		}
		
		//starts with [FIELD][Not][I]StartsWith
		if (preg_match('/^(\w+?)(Not)?(I)?StartsWith$/', $statementId, $matches)) {
			$stmt = new StartsWithStatementBuilder($this->driver, $this->entity);
			return $this->stmt($matches[0], $stmt->build($matches), Statement::type($this->buildListExpression($this->entity)));
		}
		
		//ends with [FIELD][Not][I]EndsWith
		if (preg_match('/^(\w+?)(Not)?(I)?EndsWith$/', $statementId, $matches)) {
			$stmt = new EndsWithStatementBuilder($this->driver, $this->entity);
			return $this->stmt($matches[0], $stmt->build($matches), Statement::type($this->buildListExpression($this->entity)));
		}
		
		//is null [FIELD][Not]IsNull
		if (preg_match('/^(\w+?)(Not)?IsNull$/', $statementId, $matches)) {
			return $this->stmt($matches[0], $stmt->build($matches), Statement::type($this->buildListExpression($this->entity)));
		}
		
		//matches [FIELD][Not]Matches
		if (preg_match('/^(\w+?)(Not)?Matches$/', $statementId, $matches)) {
			return $this->stmt($matches[0], $stmt->build($matches), Statement::type($this->buildListExpression($this->entity)));
		}
		
		//range [FIELD][Not]Between
		if (preg_match('/^(\w+?)(Not)?Between$/', $statementId, $matches)) {
			
		}
	}
}
?>