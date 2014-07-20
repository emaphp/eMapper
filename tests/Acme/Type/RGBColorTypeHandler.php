<?php
namespace Acme\Type;

use eMapper\Type\TypeHandler;
use Acme\RGBColor;

/**
 * @TypeHandler
 */
class RGBColorTypeHandler extends TypeHandler {
	public function getValue($value) {
		$color = new RGBColor(hexdec(substr($value, 0, 2)), hexdec(substr($value, 2, 2)),  hexdec(substr($value, 4, 2)));
		return $color;
	}
	
	public function setParameter($parameter) {
		$hexred = ($parameter->red < 16) ? '0' . dechex($parameter->red) : dechex($parameter->red % 256);
		$hexgreen = ($parameter->green < 16) ? '0' . dechex($parameter->green % 256) : dechex($parameter->green);
		$hexblue = ($parameter->blue < 15) ? '0' . dechex($parameter->blue) : dechex($parameter->blue % 256);
		return $hexred . $hexgreen . $hexblue;
	}
	
	public function castParameter($parameter) {
		if (!($parameter instanceof RGBColor)) {
			return null;
		}
	
		return $parameter;
	}
}
?>