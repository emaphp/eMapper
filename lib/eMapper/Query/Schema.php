<?php
namespace eMapper\Query;

use eMapper\Reflection\ClassProfile;
use eMapper\Reflection\Profiler;

class Schema {
	/**
	 * Default table alias
	 * @var string
	 */
	const DEFAULT_ALIAS = '_t';
	
	
	/**
	 * Default context alias (association resolver)
	 * @var string
	 */
	const CONTEXT_ALIAS = '_c';
	
	/**
	 * Entity profile
	 * @var \eMapper\Reflection\ClassProfile
	 */
	protected $profile;
	
	/**
	 * Association list
	 * @var array:\eMapper\Query\Join
	 */
	protected $joins = [];
	
	/**
	 * Arguments array
	 * @var array[string]:mixed
	 */
	protected $arguments = [];
	
	public function __construct(ClassProfile $profile) {
		$this->profile = $profile;
	}
	
	/**
	 * Obtains class profile
	 * @return \eMapper\Reflection\ClassProfile
	 */
	public function getProfile() {
		return $this->profile;
	}
	
	/**
	 * Obtains schema joins as an array
	 * @return array:\eMapper\Query\Join
	 */
	public function getJoins() {
		return $this->joins;
	}
	
	/**
	 * Obtains a join by name
	 * @param string $name
	 * @return \eMapper\Query\Join
	 */
	public function getJoin($name) {
		return $this->joins[$name];
	}
	
	/**
	 * Obtains generated arguments
	 * @return array
	 */
	public function getArguments() {
		return $this->arguments;
	}
	
	/**
	 * Translates a Field instance to the corresponding column
	 * @param \eMapper\Query\Field $field
	 * @param string $alias
	 * @throws \RuntimeException
	 * @return string
	 */
	public function translate(Field $field, $alias) {
		if ($field instanceof Attr) { //entity attribute
			if (empty($this->profile))
				throw new \RuntimeException("No entity profile has been set. Attr instance could not be resolved");
			
			//check if field refers to an associated entity
			$path = $field->getPath();
			if (empty($path)) { //obtain column name from current profile
				$expression = empty($alias) ? $field->getColumnName($this->profile) : $alias . '.' . $field->getColumnName($this->profile);
				
				if ($field->getType() == null)
					$field->type($profile->getProperty($field->getName())->getType());
				
				return $expression;
			}

			//get attribute alias + referred profile
			list($attrAlias, $profile) = $this->getAttrAlias($field);
			$expression = $attrAlias . '.' . $field->getColumnName($profile);
			
			//set attribute type
			if ($field->getType() == null)
				$field->type($profile->getProperty($field->getName())->getType());
			
			return $expression;
		}
		elseif ($field instanceof Column) { //column
			$path = $field->getPath();
			$columnAlias = $field->getColumnAlias();
			
			if (empty($path)) { // no alias
				if (empty($alias))
					return empty($columnAlias) ? $field->getName() : $field->getName() . ' AS ' . $columnAlias;
				
				return empty($columnAlias) ? $alias . '.' . $field->getName() : $alias . '.' . $field->getName() . ' AS ' . $columnAlias;
			}
			
			$references = $field->getPath()[0]; //table name/alias
			return empty($columnAlias) ? $references . '.' . $field->getName() : $references . '.' . $field->getName() . ' AS ' . $columnAlias;
		}
		elseif ($field instanceof Func) { //function
			$args = $field->getArguments();
			$list = [];
			foreach ($args as $arg)
				$list[] = $arg instanceof Field ? $this->translate($arg, $alias) : $arg;
			
			$funcAlias = $field->getColumnAlias();
			return !empty($funcAlias) ? $field->getName() . '(' . implode(',', $list) . ') AS ' . $funcAlias : $field->getName() . '(' . implode(',', $list) . ')';
		}
	}
		
	public function addArgument($index, $value) {
		$this->arguments[$index] = $value;
	}
	
	protected function getJoinAlias() {
		static $counter = 0;
		return self::DEFAULT_ALIAS . $counter;
	}
	
	protected function getAttrAlias(Attr $attr) {
		//check if join is already created
		$stringPath = $attr->getStringPath();
		if (array_key_exists($stringPath, $this->joins))
			return [$this->joins[$stringPath]->getAlias(), $this->joins[$stringPath]->getProfile()];
		
		//
		$current = $this->profile;
		$parent = null;
		
		for ($i = 0; $i < count($path); $i++) {
			//build join name
			$joinPath = array_slice($path, 0, $i + 1);
			$name = implode('__', $joinPath);
			
			//check association
			if (!$current->hasAssociation($path[$i]))
				throw new \RuntimeException(sprintf("Association '%s' not found in class %s", $path[$i], $current->getReflectionClass()->getName()));
			
			//build join instance
			$association = $current->getAssociation($path[$i]);
			$related = Profiler::getClassProfile($association->getEntityClass());
			
			if (!array_key_exists($name, $this->joins))
				$this->joins[$name] = new Join($association, $related, $path, $this->getJoinAlias(), $parent);
			
			//prepare next iteration
			$current = $related;
			$parent = $name;
		}
		
		return [$this->joins[$parent]->getAlias(), $this->joins[$parent]->getProfile()];
	}
}