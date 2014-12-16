<?php
namespace eMapper\ORM\Dynamic;

use eMapper\Reflection\EntityMapper;
use eMapper\Reflection\Profiler;
use eMapper\Reflection\ClassProfile;
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
use eMapper\Engine\Generic\Driver;
use eMapper\Mapper;
use Omocha\AnnotationBag;

/**
 * The Statement class evaluates dynamic statements found in entity attributes.
 * @author emaphp
 */
class Statement extends DynamicAttribute {
	use EntityMapper;
	
	/**
	 * Entity class
	 * @var string
	 */
	protected $entity;
	
	/**
	 * Statement Id
	 * @var string
	 */
	protected $statementId;
	
	/**
	 * Statement instance
	 * @var \eMapper\Statement\Statement
	 */
	protected $statement;
	
	protected function parseMetadata(AnnotationBag $propertyAnnotations) {
		$statementId = $attribute->get('Statement')->getValue();
		
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
	
	/**
	 * Stores a new Statement instance with the given configuration
	 * @param string $query
	 * @param \eMapper\Reflection\ClassProfile $entity
	 * @param string $asList
	 */
	protected function saveStatement($query, ClassProfile $entity, $asList = true) {
		$config = array_merge(['map.type' => $asList ? $this->buildListExpression($entity) : $this->buildExpression($entity)], $this->config);		
		$this->statement = new Statement($query, $config);
	}
	
	/**
	 * Generates a Statement instance 
	 * @param \eMapper\Engine\Generic\Driver $driver
	 * @throws \UnexpectedValueException
	 */
	protected function buildStatement(Driver $driver) {
		if (isset($this->statement))
			return;
			
		$entity = Profiler::getClassProfile($this->entity);
		
		if ($this->statementId == 'findAll')
			$this->saveStatement((new FindAllStatementBuilder($entity))->build($driver), $entity);
		elseif ($this->statementId == 'findByPk')
			$this->saveStatement((new FindByPkStatementBuilder($entity))->build($driver), $entity, false);
		elseif (preg_match('/^findBy([\w]+)/', $this->statementId, $matches)) {
			$property = strtolower($matches[1]);
			$propertyProfile = $entity->getProperty($property);
			$asList = !($propertyProfile->isPrimaryKey() || $propertyProfile->isUnique());
			$this->saveStatement((new FindByStatementBuilder($entity))->build($driver, $matches), $entity, $asList);
		}
		elseif (preg_match('/^(\w+?)(Not)?Equals$/', $this->statementId, $matches)) {
			$property = strtolower($matches[1]);
			$propertyProfile = $entity->getProperty($property);
			
			if ($propertyProfile->isPrimaryKey() || $propertyProfile->isUnique())
				$asList = array_key_exists(2, $matches);
			else
				$asList = true;
			
			$this->saveStatement((new EqualStatementBuilder($entity))->build($driver, $matches), $entity, $asList);
		}
		elseif (preg_match('/^(\w+?)(Not)?(I)?Contains$/', $this->statementId, $matches))
			$this->saveStatement((new ContainStatementBuilder($entity))->build($driver, $matches), $entity);
		elseif (preg_match('/^(\w+?)(Not)?In$/', $this->statementId, $matches))
			$this->saveStatement((new InStatementBuilder($entity))->build($driver, $matches), $entity);
		elseif (preg_match('/^(\w+?)(Not)?GreaterThan(Equal)?$/', $this->statementId, $matches))
			$this->saveStatement((new GreaterThanStatementBuilder($entity))->build($driver, $matches), $entity);
		elseif (preg_match('/^(\w+?)(Not)?LessThan(Equal)?$/', $this->statementId, $matches))
			$this->setStatement((new LessThanStatementBuilder($entity))->build($driver, $matches), $entity);
		elseif (preg_match('/^(\w+?)(Not)?(I)?StartsWith$/', $this->statementId, $matches))
			$this->saveStatement((new StartsWithStatementBuilder($driver))->build($driver, $matches), $entity);
		elseif (preg_match('/^(\w+?)(Not)?(I)?EndsWith$/', $this->statementId, $matches))
			$this->saveStatement((new EndsWithStatementBuilder($entity))->build($driver, $matches), $entity);
		elseif (preg_match('/^(\w+?)Is(Not)?Null$/', $this->statementId, $matches))
			$this->saveStatement((new IsNullStatementBuilder($entity))->build($driver, $matches), $entity);
		elseif (preg_match('/^(\w+?)(Not)?(I)?Matches$/', $this->statementId, $matches))
			$this->saveStatement((new RegexStatementBuilder($entity))->build($driver, $matches), $entity);
		elseif (preg_match('/^(\w+?)(Not)?Between$/', $this->statementId, $matches))
			$this->saveStatement((new RangeStatementBuilder($entity))->build($driver, $matches), $entity);
		else
			throw new \UnexpectedValueException("Statement '{$this->statementId}' does not match any supported expression");
	}
	
	public function evaluate($row, Mapper $mapper) {
		if ($this->evaluateCondition($row, $mapper->getConfig()) === false)
			return null;
		
		$this->buildStatement($mapper->getDriver());
		return $this->mapper->merge($this->statement->getOptions())->execute($this->statement->getQuery(), $this->evaluateArguments($row));
	}
}
