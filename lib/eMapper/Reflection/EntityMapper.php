<?php
namespace eMapper\Reflection;

use eMapper\Reflection\Profile\ClassProfile;

/**
 * The EntityMapper trait provides additional methods use to build mapping expressions
 * given a class profile.
 * @author emaphp
 */
trait EntityMapper {
	/**
	 * Obtains a simple mapping expression for the given profile
	 * @param ClassProfile $entity
	 * @return string
	 */
	protected function buildExpression(ClassProfile $entity) {
		return 'obj:' . $entity->getReflectionClass()->getName();
	}
	
	/**
	 * Obtains a list mapping expression for the give profile
	 * @param ClassProfile $entity
	 * @param string $index
	 * @param string $group
	 * @return string
	 */
	protected function buildListExpression(ClassProfile $entity, $index = null, $group = null) {
		$expr = 'obj:' . $entity->getReflectionClass()->getName();
		
		if (isset($group))
			$expr .= '<' . $group . '>';	
		
		if (isset($index))
			$expr .= '[' . $index . ']';
		else
			$expr .= '[]';
		
		return $expr;
	}
}
?>