<?php
namespace eMapper\Dynamic\Package;

use eMacros\Package\Package;
use eMacros\Runtime\Comparison\Identical;
use eMacros\Runtime\Comparison\Equal;
use eMacros\Runtime\Comparison\NotIdentical;
use eMacros\Runtime\Comparison\NotEqual;
use eMacros\Runtime\Comparison\LessThan;
use eMacros\Runtime\Comparison\GreaterThan;
use eMacros\Runtime\Comparison\LessEqual;
use eMacros\Runtime\Comparison\GreaterEqual;
use eMacros\Runtime\Logical\LogicalNot;
use eMacros\Runtime\Logical\LogicalAnd;
use eMacros\Runtime\Logical\LogicalOr;
use eMacros\Runtime\Logical\LogicalIf;
use eMacros\Runtime\Logical\Cond;
use eMacros\Runtime\Binary\BinaryOr;
use eMacros\Runtime\Binary\BinaryAnd;
use eMacros\Runtime\Arithmetic\Addition;
use eMacros\Runtime\Arithmetic\Subtraction;
use eMacros\Runtime\Arithmetic\Multiplication;
use eMacros\Runtime\Arithmetic\Division;
use eMacros\Runtime\Arithmetic\Modulus;
use eMacros\Runtime\String\Concatenation;
use eMacros\Runtime\Value\ValueReturn;
use eMacros\Runtime\Argument\ArgumentGet;
use eMacros\Runtime\Argument\ArgumentExists;
use eMacros\Runtime\Argument\ArgumentCount;
use eMacros\Runtime\Argument\ArgumentList;
use eMacros\Runtime\Type\IsInstanceOf;
use eMacros\Runtime\Type\TypeOf;
use eMacros\Runtime\Type\IsEmpty;
use eMacros\Runtime\Type\IsA;
use eMacros\Runtime\PHPFunction;
use eMacros\Runtime\Value\ValueSet;
use eMacros\Runtime\Builder\ArrayBuilder;
use eMacros\Runtime\Builder\ObjectBuilder;
use eMacros\Runtime\Builder\InstanceBuilder;
use eMacros\Runtime\Type\IsType;
use eMacros\Runtime\Type\CastToType;
use eMacros\Runtime\Index\IndexGet;
use eMacros\Runtime\Index\IndexExists;
use eMacros\Runtime\Method\MethodInvoke;
use eMacros\Runtime\Callback\CallFunction;
use eMacros\Runtime\Callback\CallFunctionArray;
use eMapper\Dynamic\Runtime\PropertyGet;
use eMapper\Dynamic\Runtime\PropertyExists;
use eMapper\Dynamic\Runtime\ConfigurationGet;
use eMapper\Dynamic\Runtime\ConfigurationExists;

class CorePackage extends Package {
	public function __construct() {
		parent::__construct('Core');
		
		/**
		 * DEFAULT SYMBOLS
		 */
		$this['null'] = null;
		$this['true'] = true;
		$this['false'] = false;
		
		/**
		 * COMMON OPERATORS
		 */
		
		//comparison operators
		$this['==='] = new Identical();
		$this['=='] = new Equal();
		$this['!=='] = new NotIdentical();
		$this['!='] = new NotEqual();
		$this['<'] = new LessThan();
		$this['>'] = new GreaterThan();
		$this['<='] = new LessEqual();
		$this['>='] = new GreaterEqual();
		
		//logical operators
		$this['not'] = new LogicalNot();
		$this['and'] = new LogicalAnd();
		$this['or'] = new LogicalOr();
		$this['if'] = new LogicalIf();
		$this['cond'] = new Cond();
		
		//binary operators
		$this['|'] = new BinaryOr();
		$this['&'] = new BinaryAnd();
		
		//arithmetic operators
		$this['+'] = new Addition();
		$this['-'] = new Subtraction();
		$this['*'] = new Multiplication();
		$this['/'] = new Division();
		$this['mod'] = new Modulus();
		
		//string operators
		$this['.'] = new Concatenation();
		
		/**
		 * CUSTOM INVOKE
		 */
		$this['call-func'] = new CallFunction();
		$this['call-func-array'] = new CallFunctionArray();
		
		/**
		 * PROPERTY FUNCTIONS
		 */
		$this['#'] = new PropertyGet();
		$this['#?'] = new PropertyExists();
		
		$this->macro('/^#([\w]+)$/', function ($matches) {
			return new PropertyGet($matches[1]);
		});
		
		$this->macro('/^#([\w]+)\?$/', function ($matches) {
			return new PropertyExists($matches[1]);
		});
		
		/**
		 * CONFIGURATION FUNCTIONS
		 */
		$this['@'] = new ConfigurationGet();
		$this['@?'] = new ConfigurationExists();
		
		$this->macro('/^@([\w|\.]+)$/', function ($matches) {
			return new ConfigurationGet($matches[1]);
		});
		
		$this->macro('/^@([\w|\.]+)\?$/', function ($matches) {
			return new ConfigurationExists($matches[1]);
		});
		
		/**
		 * Sets a symbol value
		 * Examples: (:= _message "Hello") (:= _pi Math::PI)
		 * Returns: The assigned value
		 */
		$this[':='] = new ValueSet();
		
		/**
		 * Returns a symbol/literal value
		 * Examples: (<- 10) (<- "Hello") (<- _value) (<- (+ 1 1))
		 * Returns: The symbol/literal value
		 */
		$this['<-'] = new ValueReturn();
		
		/**
		 * ARGUMENT FUNCTIONS
		 */
		
		/**
		 * Obtains an argument by a given index
		 * Examples: (% 1) (% _num)
		 * Returns: mixed
		 */
		$this['%'] = new ArgumentGet();
		
		/**
		 * Checks if the given argument index is present
		 * Example: (%? 1) (%? _num)
		 * Returns: boolean
		*/
		$this['%?'] = new ArgumentExists();
		
		/**
		 * Returns the number of arguments received by the program
		 * Examples: (:= _nargs (%#))
		 * Returns: Integer
		*/
		$this['%#'] = new ArgumentCount();
		
		/**
		 * Returns program arguments as an array
		 * Examples: (:= _args (%_))
		 * Returns: Array
		*/
		$this['%_'] = new ArgumentList();
		
		/**
		 * TYPE FUNCTIONS
		 */
		
		/**
		 * Determines if a given value is an instance of a given class
		 * Examples: (instance-of _obj eMacros\Package\Package)
		 * Returns: Boolean
		 */
		$this['instance-of'] = new IsInstanceOf();
		
		/**
		 * Returns a value type
		 * Examples: (type-of "Hello") (type-of _var)
		 * Returns: String
		*/
		$this['type-of'] = new TypeOf();
		
		/**
		 * Determines if a given values evaluates to 'empty'
		 * Examples: (empty (array)) (empty 0) (empty null) (empty _var)
		 * Returns: Boolean
		*/
		$this['empty'] = new IsEmpty();
		
		/**
		 * Determines if a given value is an instance of a given class (class is passed as string)
		 * Examples: (is-a _obj "eMacros\Symbol")
		 * Returns: Boolean
		*/
		$this['is-a'] = new IsA();
		
		/**
		 * CONVERSION FUNCTIONS
		 */
		$this['strval'] = new PHPFunction('strval');
		$this['floatval'] = new PHPFunction('floatval');
		$this['intval'] = new PHPFunction('intval');
		
		//PHP 5.5
		if (function_exists('boolval')) {
			$this['boolval'] = new PHPFunction('boolval');
		}
		
		/**
		 * BUILDER FUNCTIONS
		 */
		$this['array'] = new ArrayBuilder();
		$this['new'] = new ObjectBuilder();
		$this['instance'] = new InstanceBuilder();
		
		/**
		 * Obtains a program argument
		 * Pattern: %INDEX
		 * Examples: (%0) (%3) (%10)
		 * Returns: mixed
		 */
		$this->macro('/^%([0-9]+)$/', function ($matches) {
			return new ArgumentGet(intval($matches[1]));
		});
		
		/**
		 * Checks for argument existence
		 * Pattern: %INDEX?
		 * Examples: (%0?) (%1?) (%5?)
		 * Returns: boolean
		*/
		$this->macro('/^%([0-9]+)\?$/', function ($matches) {
			return new ArgumentExists(intval($matches[1]));
		});
		
		/**
		 * Checks values types
		 * Pattern: TYPE?
		 * Examples: (int? 4) (null? _x 45) (numeric? "542" 10 "5.3")
		 * Returns: boolean
		 */
		$this->macro('/^(bool|boolean|int|integer|string|float|double|resource|object|array|numeric|scalar|null)\?$/', function ($matches) {
			if ($matches[1] == 'boolean') {
				$matches[1] = 'bool';
			}
		
			return new IsType('is_' . $matches[1]);
		});
		
		/**
		 * Casts a value to a given type
		 * Pattern: as-TYPE
		 * Exampĺes: (as-int "6457") (as-string true) (as-null 1)
		 * Returns: mixed
		*/
		$this->macro('/^as-(bool|boolean|int|integer|string|float|double|real|object|array|unset|null|binary)$/', function ($matches) {
			return new CastToType($matches[1]);
		});
		
		/**
		 * Obtains a value from a given index
		 * Pattern: @INDEX
		 * Examples: (@3 _x) (@1)
		 * Returns: mixed
		 */
		$this->macro('/^@([+|-]?[0-9]+)$/', function ($matches) {
			return new IndexGet(intval($matches[1]));
		});
			
		/**
		 * Checks if a given index exists
		 * Pattern: @INDEX?
		 * Examples: (@3? _x) (@1?)
		 * Returns: boolean
		*/
		$this->macro('/^@([+|-]?[0-9]+)\?$/', function ($matches) {
			return new IndexExists(intval($matches[1]));
		});
		
		/**
		 * METHOD INVOCATION
		 */
		
		/**
		 * Returns the value obtained after invoking an object method
		 * Examples: (-> 'just_do_it' _obj _arg1 _arg2)
		 * Returns: Mixed
		 */
		$this['->'] = new MethodInvoke();
		
		/**
		 * Calls a method with the given parameters
		 * Pattern: (->METHOD INSTANCE PARAM1 PARAM2 ...)
		 * Examples: (->format (now) "Y-m-d H:i:s")
		 * Returns: mixed
		*/
		$this->macro('/^->([a-z|A-Z|_][\w]*)$/', function ($matches) {
			//MethodInvoke
			return new MethodInvoke($matches[1]);
		});
		
		/**
		 * PHP FUNCTION
		 * 
		 * Tries to obtain a valid PHP function from expression
		 */
		$this->macro('/^[a-z|A-Z|_][\w]*$/', function ($matches) {
			$function = $matches[0];
			
			//check function existence
			if (!function_exists($function)) {
				throw new \UnexpectedValueException("Function '$function' does not exists");
			}
			
			return new PHPFunction($function);
		});
	}
}
?>