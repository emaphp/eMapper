<?php
namespace eMapper\Result;

/**
 * The ArrayType class encapsulates the array type contants used for array mapping.
 * @author emaphp
 */
abstract class ArrayType {
	/*
	 * ARRAY TYPES
	 */
	const ASSOC = MYSQLI_ASSOC;
	const NUM   = MYSQLI_NUM;
	const BOTH  = MYSQLI_BOTH;
}