<?php
namespace eMapper\Reflection;

class Facade extends \Minime\Annotations\Facade {
	public static function getAnnotations(\Reflector $Reflection) {
		return parent::getAnnotations($Reflection);
	}
}
?>