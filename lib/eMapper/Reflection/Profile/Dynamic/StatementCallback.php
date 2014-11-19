<?php
namespace eMapper\Reflection\Profile\Dynamic;

use eMapper\Reflection\EntityMapper;
use eMapper\Reflection\Profiler;
use eMapper\Mapper;
use eMapper\Engine\Generic\Driver;
use Omocha\AnnotationBag;
use eMapper\Statement\Statement;
use eMapper\Statement\Builder\StatementBuilder;
use eMapper\Statement\Builder\FindAllStatementBuilder;
use eMapper\Statement\Builder\FindByPkStatementBuilder;
use eMapper\Statement\Builder\FindByStatementBuilder;
use eMapper\Statement\Builder\EqualStatementBuilder;
use eMapper\Statement\Builder\ContainStatementBuilder;
use eMapper\Statement\Builder\InStatementBuilder;
use eMapper\Statement\Builder\GreaterThanStatementBuilder;
use eMapper\Statement\Builder\LessThanStatementBuilder;
use eMapper\Statement\Builder\StartsWithStatementBuilder;
use eMapper\Statement\Builder\EndsWithStatementBuilder;
use eMapper\Statement\Builder\IsNullStatementBuilder;
use eMapper\Statement\Builder\RegexStatementBuilder;
use eMapper\Statement\Builder\RangeStatementBuilder;

/**
 * The StatementCallback class implements the logic for evaluating named queries againts
 * a list of arguments.
 * @author emaphp
 */
class StatementCallback extends DynamicAttribute {
	use EntityMapper;
	
	/**
	 * Entity class
	 * @var string
	 */
	protected $entity;
	
	/**
	 * Statement ID
	 * @var string
	 */
	protected $statementId;
	
	/**
	 * Statement builder instance
	 * @var Statement
	 */
	protected $statement;
		
	protected function parseMetadata(AnnotationBag $attribute) {
		//obtain statement id
		$statementId = $attribute->get('Statement')->getValue();
		
		//parse entity class
		if (preg_match('/^(\w+)\.(\w+)$/', $statementId, $matches)) {
			$this->statementId = $matches[2];
			
			//get referred property
			$entity = $matches[1];
			
			try {
				new \ReflectionClass($entity);
			}
			catch (\ReflectionException $re) {
				//get current namespace
				$currentNamespace = $this->reflectionProperty->getDeclaringClass()->getNamespaceName();
				$entity = $currentNamespace . '\\' . $entity;
			}
			
			$this->entity = $entity;
		}
		else {
			$this->entity = $this->reflectionProperty->getDeclaringClass()->getName();
			$this->statementId = $statementId;
		}
	}
	
	protected function setStatement($query, $entity,  $as_list = true) {
		$config = array_merge(['map.type' => $as_list ? $this->buildListExpression($entity) : $this->buildExpression($entity)], $this->config);		
		$this->statement = new Statement($query, $config);
	}
	
	protected function buildStatement(Driver $driver) {
		if (isset($this->statement))
			return;
		
		//get entity profile
		$entity = Profiler::getClassProfile($this->entity);
		
		if ($this->statementId == 'findAll')
			$this->setStatement((new FindAllStatementBuilder($entity))->build($driver), $entity);
		elseif ($this->statementId == 'findByPk')
			$this->setStatement((new FindByPkStatementBuilder($entity))->build($driver), $entity, false);
		elseif (preg_match('/^findBy([\w]+)/', $this->statementId, $matches)) {
			$property = strtolower($matches[1]);
			$propertyProfile = $entity->getProperty($property);
			$as_table = !($propertyProfile->isPrimaryKey() || $propertyProfile->isUnique());
			$this->setStatement((new FindByStatementBuilder($entity))->build($driver, $matches), $entity, $as_table);
		}
		elseif (preg_match('/^(\w+?)(Not)?Equals$/', $this->statementId, $matches)) {
			$property = strtolower($matches[1]);
			$propertyProfile = $entity->getProperty($property);
			
			if ($propertyProfile->isPrimaryKey() || $propertyProfile->isUnique())
				$as_table = array_key_exists(2, $matches);
			else
				$as_table = true;
			
			$this->setStatement((new EqualStatementBuilder($entity))->build($driver, $matches), $entity, $as_table);
		}
		elseif (preg_match('/^(\w+?)(Not)?(I)?Contains$/', $this->statementId, $matches)) {
			$this->setStatement((new ContainStatementBuilder($entity))->build($driver, $matches), $entity);
		}
		elseif (preg_match('/^(\w+?)(Not)?In$/', $this->statementId, $matches)) {
			$this->setStatement((new InStatementBuilder($entity))->build($driver, $matches), $entity);
		}
		elseif (preg_match('/^(\w+?)(Not)?GreaterThan(Equal)?$/', $this->statementId, $matches)) {
			$this->setStatement((new GreaterThanStatementBuilder($entity))->build($driver, $matches), $entity);
		}
		elseif (preg_match('/^(\w+?)(Not)?LessThan(Equal)?$/', $this->statementId, $matches)) {
			$this->setStatement((new LessThanStatementBuilder($entity))->build($driver, $matches), $entity);
		}
		elseif (preg_match('/^(\w+?)(Not)?(I)?StartsWith$/', $this->statementId, $matches)) {
			$this->setStatement((new StartsWithStatementBuilder($driver))->build($driver, $matches), $entity);
		}
		elseif (preg_match('/^(\w+?)(Not)?(I)?EndsWith$/', $this->statementId, $matches)) {
			$this->setStatement((new EndsWithStatementBuilder($entity))->build($driver, $matches), $entity);
		}
		elseif (preg_match('/^(\w+?)Is(Not)?Null$/', $this->statementId, $matches)) {
			$this->setStatement((new IsNullStatementBuilder($entity))->build($driver, $matches), $entity);
		}
		elseif (preg_match('/^(\w+?)(Not)?(I)?Matches$/', $this->statementId, $matches)) {
			$this->setStatement((new RegexStatementBuilder($entity))->build($driver, $matches), $entity);
		}
		elseif (preg_match('/^(\w+?)(Not)?Between$/', $this->statementId, $matches)) {
			$this->setStatement((new RangeStatementBuilder($entity))->build($driver, $matches), $entity);
		}
		else
			throw new \UnexpectedValueException("Statement '{$this->statementId}' does not match any supported expression");
	}
	
	public function evaluate($row, Mapper $mapper) {
		//evaluate condition
		if ($this->checkCondition($row, $mapper->getConfig()) === false)
			return null;
		
		//build statement
		$this->buildStatement($mapper->getDriver());
		
		//build argument list
		$args = $this->evaluateArgs($row);
		array_unshift($args, $this->statement->getQuery());
		
		//invoke statement
		return call_user_func_array([$mapper->merge($this->statement->getOptions()), 'query'], $args);
	}
}
?>