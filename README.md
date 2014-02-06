eMapper
==============

**The Extensible Mapper Package for PHP**

<br/>
**Author**: Emmanuel Antico
<br/>
**Version**: 3.0.0

<br/>
Latest modifications
------------------
<br/>
2014-??-?? - Version 3.0.0

  * Added: Support for SQLite and PostgreSQL
  * Deprecated: Models are now replaced for ResultMaps and Entities
  * Added: Entity classes allow to obtain customized objects through annotations
  * Added: Support for dynamic SQL clauses through eMacros
  * Added: Dynamic attributes
  * Added: Grouping
  * Added: Index (and group) callbacks
  * Fixed: Lots of bugs from previous version

<br/>
Dependencies
--------------
<br/>
- PHP >= 5.4
- Marcio Almada's [annotations](https://github.com/marcioAlmada/annotations "") package
- [eMacros](https://github.com/emaphp/eMacros "")
 
<br/>
Installation
--------------
<br/>
**Installation through Composer**
<br/>
```javascript
{
    "require": {
        "emapper/emapper" : "3.*"
    }
}
```

<br/>
Introduction
------------

<br/>
***eMapper*** is a PHP library aimed to provide a simple, powerful and highly customizable data mapping tool. It comes with some interesting features like:

- **Customized mapping**: Results can be mapped to a desired type through simple mapping expressions.
- **Indexation and Grouping**: Lists can be indexed or grouped together using a column value.
- **Custom types**: Developers can design their own types and custom type handlers.
- **Cache providers**: Obtained data can be stored in cache using APC or Memcache.
- **Dynamic SQL**: Queries can contain Dynamic SQL clauses writted in eMacros.


<br/>
First steps
-----------
<br/>
***Note***: Most of these examples use the *MySQLMapper* class. This is because the main difference between mapper classes is only their name. Check the appendix *Database Providers* for details of how to work with other databases.

<br/>
We'll begin by creating a new *MySQLMapper* instance. This class constructor receives the database name, host name, and user credentials.
```php
<?php
//composer autoloader
require __DIR__ . "/vendor/autoload.php";

use eMapper\Engine\MySQL\MySQLMapper;

//mysql mapper class
$mapper = new MySQLMapper('my_db', 'localhost', 'my_user', 'my_pass');
?>
```

<br/>
Arrays
-------

<br/>
**Get a list of rows as an array**

<br/>
This example illustrates how to to execute a SQL query through the *query* method. The obtained result is returned as an array of arrays with both numeric and associative indexes.
```php
<?php
//composer autoloader
require __DIR__ . "/vendor/autoload.php";

use eMapper\Engine\MySQL\MySQLMapper;

//mysql mapper class
$mapper = new MySQLMapper('my_db', 'localhost', 'my_user', 'my_pass');

//get user list
$users = $mapper->query("SELECT * FROM users");

//do something with users

$mapper->close();
?>
```

<br/>
**Obtain a row as an associative array**

<br/>
To indicate which type is expected from a query we declare a mapping expression through the *type* method. This example specifies the desired type to *array*. The array mapper supports an additional parameter which tells the type of array to return. The obtained valued will be an associative array containing all values from that row.
```php
<?php
//composer autoloader
require __DIR__ . "/vendor/autoload.php";

use eMapper\Engine\MySQL\MySQLMapper;

$mapper = new MySQLMapper('my_db', 'localhost', 'my_user', 'my_pass');

//get user by id as associative array
$user = $mapper->type('array', MYSQLI_ASSOC)->query("SELECT * FROM users WHERE user_id = 1");

//do something with user

$mapper->close();
?>
```

<br/>

Objects
-------

<br/>
**Obtain a row as an object**

<br/>
To obtain a *stdClass* instance from a row we simply set the desired type to *object* (or *obj*).

```php
<?php
//composer autoloader
require __DIR__ . "/vendor/autoload.php";

use eMapper\Engine\MySQL\MySQLMapper;

$mapper = new MySQLMapper('my_db', 'localhost', 'my_user', 'my_pass');

//get user as object (stdClass)
$user = $mapper->type('object')->query("SELECT * FROM users WHERE user_id = 1");

//do something with user

$mapper->close();
?>
```

<br/>
**Obtain a row as a custom class object**

<br/>
It is possible to define the object class through the mapping expression. For this purpose we have designed a *User* class within the *Acme* namespace.
```php
<?php
namespace Acme;

class User {
    public $user_id;
    public $name;
    public $password;
    public $email;
}
```
An object class must be specified right after the desired type adding a ':' between them.

```php
<?php
//composer autoloader
require __DIR__ . "/vendor/autoload.php";

use eMapper\Engine\MySQL\MySQLMapper;

$mapper = new MySQLMapper('my_db', 'localhost', 'my_user', 'my_pass');

//get user by id as an instance of Acme\User
$user = $mapper->type('obj:Acme\User')->query("SELECT * FROM users WHERE user_id = 1");

//do something with user

$mapper->close();
?>
```
<br/>
Scalars
-------

<br/>
**Obtain a column value as a string**

<br/>
Mapping expressions also support simple data types.
```php
<?php
//composer autoloader
require __DIR__ . "/vendor/autoload.php";

use eMapper\Engine\MySQL\MySQLMapper;

$mapper = new MySQLMapper('my_db', 'localhost', 'my_user', 'my_pass');

//get username of user with id = 1
$username = $mapper->type('string')->query("SELECT name FROM users WHERE user_id = 1");

$mapper->close();
?>
```
<br/>
**Obtain a custom column value as an integer**

<br/>
By default, scalars are obtained reading from the first column. We can change this behaviour by specifying the column name as a second parameter.

```php
<?php
//composer autoloader
require __DIR__ . "/vendor/autoload.php";

use eMapper\Engine\MySQL\MySQLMapper;

$mapper = new MySQLMapper('my_db', 'localhost', 'my_user', 'my_pass');

//get user id of user with username = 'jdoe'
$username = $mapper->type('int', 'user_id')->query("SELECT * FROM users WHERE username = 'jdoe'");

$mapper->close();
?>
```
<br/>
Dates
-----

<br/>
**Obtain a column value as a DateTime instance**

<br/>
Columns of type *DATETIME*, *TIMESTAMP*, etc. are mapped by default to instances of **DateTime**.
```php
<?php
//composer autoloader
require __DIR__ . "/vendor/autoload.php";

use eMapper\Engine\MySQL\MySQLMapper;

$mapper = new MySQLMapper('my_db', 'localhost', 'my_user', 'my_pass');

//get sale date as a DateTime object
$date = $mapper->type('dt', 'sale_sate')->query("SELECT * FROM sales WHERE sale_id = 324");

$mapper->close();
?>
```

<br/>
Lists
-----

<br/>
**Obtain a list of integers**

<br/>
We can also get lists of a given type by adding brackets at the end of the mapping expression. For example, *integer[]* will return a list of integers.
```php
<?php
//composer autoloader
require __DIR__ . "/vendor/autoload.php";

use eMapper\Engine\MySQL\MySQLMapper;

$mapper = new MySQLMapper('my_db', 'localhost', 'my_user', 'my_pass');

//get users ids as a list
$user_ids = $mapper->type('integer[]')->query("SELECT user_id FROM users");

$mapper->close();
?>
```

<br/>
**Obtain a list of strings from a column**

```php
<?php
//composer autoloader
require __DIR__ . "/vendor/autoload.php";

use eMapper\Engine\MySQL\MySQLMapper;

$mapper = new MySQLMapper('my_db', 'localhost', 'my_user', 'my_pass');

//get users names as a list
$usernames = $mapper->type('str[]', 'username')->query("SELECT * FROM users");

$mapper->close();
?>
```

<br/>
Indexed lists
-------------

<br/>
**Obtain a list of objects indexed by column**

<br/>
Lists of arrays/objects can be indexed by a given column by specifying that column name between brackets on the mapping expression. The following code returns an array of objects where each key is the corresponding value of the column *user_id* for that row.
```php
<?php
//composer autoloader
require __DIR__ . "/vendor/autoload.php";

use eMapper\Engine\MySQL\MySQLMapper;

$mapper = new MySQLMapper('my_db', 'localhost', 'my_user', 'my_pass');

//get user list as an array of objects indexed by user id
$users = $mapper->type('object[user_id]')->query("SELECT * FROM users");

//do something with users

$mapper->close();
?>
```
<br/>
**Obtain a list of arrays indexed by column**

This syntax is supported by the array mapper as well.

```php
<?php
//composer autoloader
require __DIR__ . "/vendor/autoload.php";

use eMapper\Engine\MySQL\MySQLMapper;

$mapper = new MySQLMapper('my_db', 'localhost', 'my_user', 'my_pass');

//get users as an array of associative arrays indexed by user id
$users = $mapper->type('array[user_id]', MYSQLI_ASSOC)->query("SELECT * FROM users");

//do something with users

$mapper->close();
?>
```
Remember that you can use only columns which are present on the result set. If we want a list of numeric arrays the index column must be specified as an integer.
```php
<?php
//composer autoloader
require __DIR__ . "/vendor/autoload.php";

use eMapper\Engine\MySQL\MySQLMapper;

$mapper = new MySQLMapper('my_db', 'localhost', 'my_user', 'my_pass');

//get users as an array of numeric arrays indexed by the first column
$users = $mapper->type('array[0]', MYSQLI_NUM)->query("SELECT * FROM users");

//do something with users

$mapper->close();
?>
```

<br/>
**Obtain a list of objects indexed by column with a custom type**

<br/>
Index type can be declared by adding a type specifier right after its name. If no type is specified then the one associated with the column is used.
```php
<?php
//composer autoloader
require __DIR__ . "/vendor/autoload.php";

use eMapper\Engine\MySQL\MySQLMapper;

$mapper = new MySQLMapper('my_db', 'localhost', 'my_user', 'my_pass');

//get users as an array of stdClass instances indexed by user id casted to string
$users = $mapper->type('object[user_id:string]')->query("SELECT * FROM users");

//do something with users

$mapper->close();
?>
```

<br/>
Grouping
-------

<br/>
Queries
-------

<br/>
**Passing parameters to a query**

<br/>
When calling the ***query*** method we can specify an arbitrary number of parameters. Each of these parameters are referenced within the query string with an expression that contains a leading ***%*** character followed by a type specifier between braces.
```php
<?php
//composer autoloader
require __DIR__ . "/vendor/autoload.php";

use eMapper\Engine\MySQL\MySQLMapper;

$mapper = new MySQLMapper('my_db', 'localhost', 'my_user', 'my_pass');

//obtain user with id = 1
$user = $mapper->type('obj')->query("SELECT * FROM users WHERE user_id = %{int}", 1);

$mapper->close();
?>
```
The next example shows how to use type specifiers to generate an insertion query.
```php
<?php
//composer autoloader
require __DIR__ . "/vendor/autoload.php";

use eMapper\Engine\MySQL\MySQLMapper;

$mapper = new MySQLMapper('my_db', 'localhost', 'my_user', 'my_pass');

//user values
$username = 'jdoe';
$password = sha1('jhon123');
$is_admin = false;
$image = file_get_contents('photo.jpg');

//insert data ('x' is short for 'blob')
$mapper->query("INSERT INTO users (username, password, is_admin, image) VALUES (%{s}, %{s}, %{b}, %{x})", $username, $password, $is_admin, $image);

$mapper->close();
?>
```

<br/>
**Passing arrays as parameters to a query**

<br/>
When passing an array, all values are converted to the specified type and then joined together. This is useful when doing a search using the **IN** clause.
```php
<?php
//composer autoloader
require __DIR__ . "/vendor/autoload.php";

use eMapper\Engine\MySQL\MySQLMapper;

$mapper = new MySQLMapper('my_db', 'localhost', 'my_user', 'my_pass');

//execute query: SELECT * FROM products WHERE code IN ('MXP412', 'TRY235', 'OFR255')
$products = $mapper->query("SELECT * FROM products WHERE code IN (%{s})", array('MXP412', 'TRY235', 'OFR255'));

$mapper->close();
?>
```
<br/>
**Specifying parameters by order of appearance**

<br/>
There's an additional syntax that allow us to refer to a parameter by its order of appearance. Instead of the desired type we use a number which identifies the parameter in the list and (optionally) a type specifier.
```php
<?php
//composer autoloader
require __DIR__ . "/vendor/autoload.php";

use eMapper\Engine\MySQL\MySQLMapper;

$mapper = new MySQLMapper('my_db', 'localhost', 'my_user', 'my_pass');

//first parameter is %{0}
$products = $mapper->type('obj[product_id]')->query("SELECT * FROM products WHERE product_id = %{1} OR product_code = %{0:s}", 'PHN00098', 3);

//do something with products

$mapper->close();
?>
```
We can also tell from which subindex must be obtained a value. A subindex must be appended right after the parameter index and placed between brackets.

```php
<?php
//composer autoloader
require __DIR__ . "/vendor/autoload.php";

use eMapper\Engine\MySQL\MySQLMapper;

$mapper = new MySQLMapper('my_db', 'localhost', 'my_user', 'my_pass');
$param_list = array('id' => 1, 'jdoe', 'david');

$users = $mapper->type('obj[]')->query("SELECT * FROM users WHERE user_id = %{0[id]} OR username = %{0[1]:str} OR username = %{0[2]:str}", $param_list);

//do something with users

$mapper->close();
?>
```

<br/>
**Ranges**

Ranges allow to use a subset of a list passed as argument. The obtained expression is similar to use the function [array_slice](http://www.php.net/manual/en/function.array-slice.php "") on the specified array. The left value represents the offset and the right one the length.
```php
<?php
//composer autoloader
require __DIR__ . "/vendor/autoload.php";

use eMapper\Engine\MySQL\MySQLMapper;

$mapper = new MySQLMapper('my_db', 'localhost', 'my_user', 'my_pass');
$list = array(45, 23, '43', '164', 43);

//obtain a sublist with '43' and '164'
$users = $mapper->type('obj[]')->query("SELECT * FROM users WHERE user_id IN (%{0[2..2]:i})", $list);

$mapper->close();
?>
```

If one value is omitted then the corresponding limit is used:

* [..3] Obtains the first 3 elements.
* [1..] Obtains all elements except the first one.
* [..] Obtains the whole list.

<br/>
**Using objects and arrays as parameter**

<br/>
Queries also supports a syntax which obtains values from object properties (and array keys). We can refer to an object property with the '#' symbol and the object property between braces. Just like previous mapping expressions, it is also possible to specify the property type, subindex and range.

```php
<?php
//composer autoloader
require __DIR__ . "/vendor/autoload.php";

use eMapper\Engine\MySQL\MySQLMapper;

$mapper = new MySQLMapper('my_db', 'localhost', 'my_user', 'my_pass');

//user values
$user = new stdClass();
$user->username = 'jdoe';
$user->password = sha1('jhon123');
$user->is_admin = false;
$user->image = file_get_contents('photo.jpg');

//insert data
$mapper->query("INSERT INTO users (username, password, is_admin, image) VALUES (#{username}, #{password:s}, #{is_admin}, #{image:blob})", $user);

$mapper->close();
?>
```
<br/>
Statements
----------

<br/>
**Executing statements**

<br/>
A statement object represents a query which can be identified by a string ID. Statements are created by calling the *stmt* method and are invoked through the *execute* method.
```php
<?php
//composer autoloader
require __DIR__ . "/vendor/autoload.php";

use eMapper\MySQL\MySQLMapper;

$mapper = new MySQLMapper('my_db', 'localhost', 'my_user', 'my_pass');

//add statement
$mapper->stmt('findAllUsers', "SELECT * FROM users");

//execute statement
$users = $mapper->map('obj[]')->execute('findAllUsers');

$mapper->close();
?>
```
<br/>
It is possible to provide a default set of options by adding a third parameter. Statement options are generated through 2 static methods available in the *eMapper\Statement\Statement* class.

```php
<?php
//composer autoloader
require __DIR__ . "/vendor/autoload.php";

use eMapper\MySQL\MySQLMapper;
use eMapper\Statement\Statement;

$mapper = new MySQLMapper('my_db', 'localhost', 'my_user', 'my_pass');

//add statements
$mapper->stmt('findAllUsers', "SELECT * FROM users", Statement::config()->map('obj[user_id]'));
$mapper->stmt('findUserByPK', "SELECT * FROM users WHERE user_id = %{i}", Statement::map('obj'));

//execute statements
$users = $mapper->execute('findAllUsers');
$user = $mapper->execute('findUserByPK', 3);

$mapper->close();
?>
```
The main difference between these two methods is that *config* accepts an array containing a list of options as a parameter. Check the *Appendix II - Configuration options* for a complete list of available options.

<br/>
Namespaces
----------

<br/>
**Creating namespaces**

<br/>
A namespace is an object that contains statements. The main purpose of working with namespaces is allowing the programmer to manage statements trees separately. This can be helpful is mid-sized projects where a lot of queries are created and we need a way to organize them.

```php
<?php
//composer autoloader
require __DIR__ . "/vendor/autoload.php";

use eMapper\MySQL\MySQLMapper;
use eMapper\Statement\Statement;

$mapper = new MySQLMapper('my_db', 'localhost', 'my_user', 'my_pass');

//create 'users' namespace
$usersNamespace = $mapper->ns('users');

//method ns returns a reference to the new namespace
$usersNamespace
->stmt('findAll', "SELECT * FROM users", Statement::map('obj[user_id]'))
//calls to the stmt method can be chained
->stmt('findByPK', "SELECT * FROM users WHERE user_id = %{i}", Statement::map('obj'))
//this creates an inner namespace within 'users'
->ns('admin')
->stmt("delete", "DELETE FROM users WHERE user_id = %{i}");

//execute statements
$users = $mapper->execute('users.findAll');
$user = $mapper->execute('users.findByPK', 3);
$mapper->execute('users.admin.delete', 0);

$mapper->close();
?>
```

<br/>
**Custom namespaces**

<br/>
Another way to organize statements is creating custom namespaces classes to avoid keeping all of them on the same file. Custom namespaces must extend the *eMapper\Statement\StatementNamespace* class.

```php
<?php
namespace Acme;

use eMapper\Statement\StatementNamespace;
use eMapper\Statement\Statement;

class UsersNamespace extends StatementNamespace {
	public function __construct() {
		parent::__construct('users');
		
		$this->stmt('findByUsername',
		            "SELECT * FROM users WHERE user_name = %{s}");
		            
		$this->stmt('findByPK',
		            "SELECT * FROM users WHERE user_id = %{i}",
		            Statement::map('obj'));
		            
		$this->stmt('findAll',
		            "SELECT * FROM users",
		            Statement::config()->map('obj[user_id]'));
	}
}
?>
```
Namespaces are added through the *addNamespace* method.
```php
<?php
//composer autoloader
require __DIR__ . "/vendor/autoload.php";

use eMapper\MySQL\MySQLMapper;
use Acme\UsersNamespace;

//create mapper
$mapper = new MySQLMapper('my_db', 'localhost', 'my_user', 'my_pass');
$mapper->addNamespace(new UsersNamespace());

//get user
$user = $mapper->execute('users.findByPK', 4);

$mapper->close();
```

<br/>
Raw results
-----------

<br/>
**Obtaining results as resources**

<br/>
Using the ***sql*** method we can obtain ***mysqli_result*** objects directly from queries. This method ignores all configuration methods appended before it.
```php
<?php
//composer autoloader
require __DIR__ . "/vendor/autoload.php";

use eMapper\MySQL\MySQLMapper;

$mapper = new MySQLMapper('my_db', 'localhost', 'my_user', 'my_pass');
$result = $mapper->sql("SELECT user_id, username FROM users WHERE user_id = %{i}", 5);

while (($row = $result->fetch_array()) != null) {
    //...
}

//avoid 'synchronized' bug in older mysqli
$mapper->free_result($result);
$mapper->close();
?>
```

<br/>
Stored procedures
-----------------

<br/>
**Calling stored procedures**

<br/>
Stored procedures are routines available in MySQL that manage persistence logic. These routines can be invoked directly from a mapper instance thanks to a feature that automatically translates a non-declared method invocation to a query through *overloading* ([http://php.net/manual/en/language.oop5.overloading.php](http://php.net/manual/en/language.oop5.overloading.php "")). The generated query will contain the procedure name and all parameters provided.

```php
<?php
//composer autoloader
require __DIR__ . "/vendor/autoload.php";

use eMapper\MySQL\MySQLMapper;

$mapper = new MySQLMapper('my_db', 'localhost', 'my_user', 'my_pass');

//SQL: CALL FindUserByUsername('jdoe')
$user = $mapper->map('object')->FindUserByUsername('jdoe');

$mapper->close();
?>
```
We can specify parameter types through the ***ptypes*** method if necessary. Each one of the arguments corresponds to a parameter type. The code below calls a stored procedure specifying the argument types (username:string, password:string, is_admin:boolean). The result will then be mapped to an integer.
```php
<?php
//composer autoloader
require __DIR__ . "/vendor/autoload.php";

use eMapper\MySQL\MySQLMapper;

$mapper = new MySQLMapper('my_db', 'localhost', 'my_user', 'my_pass');

//SQL: CALL InsertNewUser('anndoe', 'pass123', TRUE)
$user_id = $mapper
->map('int')
->ptypes('s', 's', 'b')
->InsertNewUser('anndoe', 'pass123', 1);

$mapper->close();
?>
```
In most cases, stored procedures are declared using the database prefix. In order to avoid specifying the stored procedure prefix for every call, we can set the option *db.prefix* to store the current database prefix. Whenever we try to execute a stored procedure this prefix will be automatically appended in front of it.
```php
<?php
//composer autoloader
require __DIR__ . "/vendor/autoload.php";

use eMapper\MySQL\MySQLMapper;

$mapper = new MySQLMapper('my_db', 'localhost', 'my_user', 'my_pass');

//set database prefix
$mapper->setPrefix('EMP_');
//OR $mapper->set('db.prefix', 'EMP_');

//SQL: CALL EMP_InsertImage('My Image', x...);
$image_id = $mapper
->map('integer')
->ptypes('string', 'blob')
->InsertImage('My Image', file_get_contents('image.png'));

$mapper->close();
?>
```


<br/>
Dynamic SQL
-----------

<br/>
**Calling a custom callback from within a query**

<br/>
It is possible to define custom callbacks in order to populate a SQL query with a custom string. These callbacks are defined by invoking the *dynamic* method with a callback id (as string) and a Closure object. Callbacks are called from within the query by inserting the callback id between double brackets. These callbacks receive one array containing all query arguments.

```php
<?php
//composer autoloader
require __DIR__ . "/vendor/autoload.php";

use eMapper\MySQL\MySQLMapper;

$mapper = new MySQLMapper('my_db', 'localhost', 'my_user', 'my_pass');
$result = $mapper
->map('obj')
->dynamic('condition', function ($args) {
    //put condition according with argument type
    if (is_int($args[0])) {
        return 'user_id = %{i}';
    }
    else {
        return 'user_name = %{s}';
    }
})->query("SELECT * FROM user_id WHERE [[condition]]", 1);

```
You can add an arbitrary number of callbacks. These are stored inside the mapper configuration array using their callback id with the prefix 'dynamic.' as key. You could also provide a default value in case a callback happens to be undefined. Default values are inserted right after the callback id and a double pipe (||). If the callback is defined then the default value is sent as a second parameter.

```php
<?php
//composer autoloader
require __DIR__ . "/vendor/autoload.php";

use eMapper\MySQL\MySQLMapper;

//define order column
$order_column = 'user_name';

$mapper = new MySQLMapper('my_db', 'localhost', 'my_user', 'my_pass');
$result = $mapper
->map('obj')
->dynamic('order', function ($args, $default) use ($order_column) {
    //return user_name ASC
    return isset($order_column) ? $order_column . ' ASC' : $default;
})
//order by id by default
->query("SELECT * FROM users ORDER BY [[order||user_id ASC]]");

```
<br/>
Cache
-----

<br/>
**Using cache providers**

<br/>
Cache providers provide a generic way to store and retrieve values using libraries like **APC**, **Memcache** and **Memcached**. The first step consist in defining the cache provider to use through the *setProvider* method. Then, we append an invocation to the ***cache*** method before running a query/statement/procedure. This method expects the *cache identification key* and its *expiration time* (TTL: time to live).
```php
<?php
//composer autoloader
require __DIR__ . "/vendor/autoload.php";

use eMapper\MySQL\MySQLMapper;
use eMapper\Cache\APCProvider;

$mapper = new MySQLMapper('my_db', 'localhost', 'my_user', 'my_pass');

//set provider
$mapper->setProvider(new APCProvider());

//store user list in apc cache for 5 minutes
$users = $mapper->cache('USERS_ALL', 300)->query("SELECT * FROM users");

$mapper->close();
?>
```
When using the cache extension the behaviour of the mapper object is modified in the following way:

 - If a value with the given key is found in cache then that value is returned.
 - If no value is found, ***query*** is executed as usual. If the query returns 1 or more rows then the result is mapped and then stored in cache. That value is then returned.

<br/>
This example uses Memcache instead of APC and also illustrates how to create a **dynamic cache key**. The parameters used to run the query are also used to build the cache key. As a result, the returned value will be stored using the key *USER_6*.

```php
<?php
//composer autoloader
require __DIR__ . "/vendor/autoload.php";

use eMapper\MySQL\MySQLMapper;
use eMapper\Cache\MemcacheProvider;

$mapper = new MySQLMapper('my_db', 'localhost', 'my_user', 'my_pass');

//set provider
$mapper->setProvider(new MemcacheProvider('65.132.12.4', 13412));

//get user and store in cache
$user = $mapper
->cache('USER_%{i}', 120)
->map('array')
->query("SELECT * FROM users where user_id = %{i}", 6);

$mapper->close();
?>
```

<br/>
Configuration
--------------
<br/>
In order to accomplish its functionality, a mapper class generates copies of itself dynamically whenever certain methods are invoked. This means that when calling methods like ***cache***, ***map***, etc. the object clones itself and applies a new configuration. These configuration values are transient, which means that they don't apply to the main instance from they were created. For example, the ***map*** method sets the option *'map.type'* to the requested value type. Alternatively, we can declare transient values using the ***option*** method.

```php
<?php
//composer autoloader
require __DIR__ . "/vendor/autoload.php";

use eMapper\MySQL\MySQLMapper;

$mapper = new MySQLMapper('my_db', 'localhost', 'my_user', 'my_pass');
$four = $mapper->option('map.type', 'integer')->query("SELECT 2 + 2");
?>
```
<br/>
An mapper instance can also store customized configuration values of any type. To store a configuration value within an object we call the ***set*** method specifying both name and value.
```php
<?php
//composer autoloader
require __DIR__ . "/vendor/autoload.php";

use eMapper\MySQL\MySQLMapper;

$mapper = new MySQLMapper('my_db', 'localhost', 'my_user', 'my_pass');
$mapper->set('my_value', 'foo');
?>
```
Values defined with the ***set*** method are permanent. This means that all instances generated from that object will keep those values. Additionally, values generated with this method can be obtained by calling ***get*** with the configuration key name.

<br/>
Configuration values can also be used within queries. We do this by surrounding the configuration key between braces after a leading *'@'* character.
```php
<?php
//composer autoloader
require __DIR__ . "/vendor/autoload.php";

use eMapper\MySQL\MySQLMapper;

$mapper = new MySQLMapper('my_db', 'localhost', 'my_user', 'my_pass');
$mapper->set('my_table', 'users');
$user = $mapper->map('obj')->query("SELECT * FROM @{my_table} WHERE user_id = 2");
?>
```
If a given configuration value cannot be converted to string the expression is left blank.

<br/>
The 'each' method
-----------------

<br/>
The ***each*** method allow us to apply a user defined function to every row returned from a query. This method sets the configuration property 'callback.each', which must be assigned to a valid callback. The user function receives the row obtained and the mapper instance. This example illustrates how to use this method in order to dynamically generate 2 extra attributes on an obtained row.

```php
<?php
//composer autoloader
require __DIR__ . "/vendor/autoload.php";

use eMapper\MySQL\MySQLMapper;

$mapper = new MySQLMapper('my_db', 'localhost', 'my_user', 'my_pass');

//find user
$user = $mapper
->each(function (&$user, $mapper) {
    $user->age = (int) $user->birth_date->diff(new \DateTime())->format('%y');
    $user->profile = $mapper->execute('profiles.findByUserId', $user->user_id);
})
->execute('users.findByPK', 3);

$mapper->close();
?>
```
<br/>
Filters
-------

<br/>
The ***filter*** method sets a callback that determines which objects are removed from an obtained list. Using filters is pretty similar to apply a user-defined function to an array through the [array_filter](http://www.php.net/manual/en/function.array-filter.php "") function.

```php
<?php
//composer autoloader
require __DIR__ . "/vendor/autoload.php";

use eMapper\MySQL\MySQLMapper;

$mapper = new MySQLMapper('my_db', 'localhost', 'my_user', 'my_pass');

//find users according to a filter
$users = $mapper
->each(function (&$user, $mapper) {
    $user->age = (int) $user->birth_date->diff(new \DateTime())->format('%y');
    $user->profile = $mapper->execute('profiles.findByUserId', $user->user_id);
})
->filter(function ($user) {
    //filter users that are 60 years old (or older)
    return $user->age < 60;
})
->execute('users.findAll');

$mapper->close();
?>
```
If a filter is applied to a non-list and the filter evaluates to false then NULL is returned.

<br/>
Custom type handlers
--------------------

<br/>
Custom type handlers provide the logic to store and retrieve user defined types to/from a column. These classes must extend the *eMapper\Type\Typehandler* class and implement these functionalities:

 - Read a value from a column and obtain a valid class instance.
 - Generate a valid expression from an instance to put inside a query.

<br/>
The next examples will illustrate the implementation of a custom type class named *Acme\RGBColor*. Instances of this class will be stored in a column defined as CHAR(6). The column will hold the hexadecimal representation of a color, that is, the proper string which describes its red, green and blue components.

<br/>
**Custom type: the RGBColor class**

```php
<?php
namespace Acme;

class RGBColor {
	public $red;
	public $green;
	public $blue;

	public function __construct($red, $green, $blue) {
		$this->red = $red;
		$this->green = $green;
		$this->blue = $blue;
	}
}
```

<br/>
**Type handler: the RGBColorTypeHandler class**

```php
<?php
namespace Acme;

use eMapper\Type\TypeHandler;
use Acme\RGBColor;

class RGBColorTypeHandler extends TypeHandler {
	/**
	 * Translates a RGBColor instance to a valid string expression
	 */
	public function setParameter($parameter) {
	    //get red component
	    if ($parameter->red < 16) {
	        $hexred = '0' . dechex($parameter->red);
	    }
	    else {
    	    $hexred = dechex($parameter->red % 256);
	    }
	    
        //get green component
	    if ($parameter->green < 16) {
	        $hexgreen = '0' . dechex($parameter->green);
	    }
	    else {
    	    $hexgreen = dechex($parameter->green % 256);
	    }
	    
        //get blue component
	    if ($parameter->blue < 16) {
	        $hexblue = '0' . dechex($parameter->blue);
	    }
	    else {
    	    $hexblue = dechex($parameter->blue % 256);
	    }
	    
		return $hexred . $hexgreen . $hexblue;
	}

	/**
	 * Generates a new RGBColor instance from a string
	 */
	public function getValue($value) {
		return new RGBColor(hexdec(substr($value, 0, 2)),
		                    hexdec(substr($value, 2, 2)),
		                    hexdec(substr($value, 4, 2)));
	}

	/**
	 * Tries to convert a value to a RGBColor instance
	 */
	public function castParameter($parameter) {
		if (!($parameter instanceof RGBColor)) {
			return null;
		}

		return $parameter;
	}
}
```
<br/>
Some things to notice about type handlers:

- The method *setParameter* receives a RGBColor instance and builds a string expression that is then inserted on a query string.
- The method *getValue* obtains a column content and is responsible of generating a new RGBColor instance from it.
- Additionally, we can convert an incoming query parameter to a RGBColor instance. The method *castParameter* is called whenever a query parameter uses *Acme\RGBColor* as a type specifier. The actual implementation chooses to avoid any parameter that is not a valid RGBColor instance and returning null as a replacement.

<br/>
**Declaring a custom type**

<br/>
Customs types are declared through the *addType* method. This methods expects the custom type full name, its type handler and an optional alias.

```php
<?php
//composer autoloader
require __DIR__ . "/vendor/autoload.php";

use eMapper\MySQL\MySQLMapper;
use Acme\RGBColorTypeHandler;

$mapper = new MySQLMapper('my_db', 'localhost', 'my_user', 'my_pass');
$mapper->addType('Acme\RGBColor', new RGBColorTypeHandler(), 'color');
?>
```

<br/>
By creating an alias we can now use 'color' as a type specifier. This is in fact not necessary as we already define a type handler for the class itself.

```php
<?php
$red = new \stdClass;
$red->name = 'red';
$red->rgb = new RGBColor(255, 0, 0);

$mapper->query("INSERT INTO palette (name, rgb) VALUES (#{name}, #{rgb:color})", $red);
?>
```

<br/>
The real advantage comes when used as a mapping expression.

```php
<?php
//composer autoloader
require __DIR__ . "/vendor/autoload.php";

use eMapper\MySQL\MySQLMapper;
use Acme\RGBColorTypeHandler;

$mapper = new MySQLMapper('my_db', 'localhost', 'my_user', 'my_pass');
$mapper->addType('Acme\RGBColor', new RGBColorTypeHandler(), 'color');

$red = $mapper->map('color')->query("SELECT rgb FROM palette WHERE name = 'red'");
?>
```

<br/>
Exceptions
----------

<br/>
All library exceptions extend the ***MySQLMapperException*** class.
```php
<?php
//composer autoloader
require __DIR__ . "/vendor/autoload.php";

use eMapper\MySQL\MySQLMapper;
use eMapper\Exception\MySQL\MySQLMapperException;

try {
    //ERROR: empty user
	$mapper = new MySQLMapper('my_db', 'localhost', '', 'my_pass');
	
	//run query
	$users = $mapper->map('array[user_id]', MYSQLI_ASSOC)->query("SELECT * FROM users");
	
	$mapper->close();
}
catch (MySQLMapperException $me) {
	echo "Unexpected error: " . $me->getMessage();
}
?>
```
<br/>
If the connection failed due to a wrong configuration value, then a ***MySQLConnectionException*** is thrown.
```php
<?php
//composer autoloader
require __DIR__ . "/vendor/autoload.php";

use eMapper\MySQL\MySQLMapper;
use eMapper\Exception\MySQL\MySQLMapperException;
use eMapper\Exception\MySQL\MySQLConnectionException;

try {
    //ERROR: fake_user does not exists!
	$mapper = new MySQLMapper('my_db', 'localhost', 'fake_user', 'my_pass');
	
	//run query
	$users = $mapper->map('array[user_id]', MYSQLI_ASSOC)->query("SELECT * FROM users");
	
	$mapper->close();
}
catch (MySQLConnectionException $ce) {
	echo "Connection error: " . $me->getMessage();
}
catch (MySQLMapperException $me) {
	echo "Unexpected error: " . $me->getMessage();
}
?>
```

The ***MySQLQueryException*** is thrown only when a query syntax error is found.

```php
<?php
//composer autoloader
require __DIR__ . "/vendor/autoload.php";

use eMapper\MySQL\MySQLMapper;
use eMapper\Exception\MySQL\MySQLMapperException;
use eMapper\Exception\MySQL\MySQLQueryException;

try {
	$mapper = new MySQLMapper('my_db', 'localhost', 'my_user', 'my_pass');
	
	//ERROR: no columns
	$users = $mapper->map('array[user_id]', MYSQLI_ASSOC)->query("SELECT FROM users");
	
	$mapper->close();
}
catch (MySQLQueryException $qe) {
	echo "SQL query error: " . $qe->getMessage();
}
catch (MySQLMapperException $me) {
	echo "Unexpected error: " . $me->getMessage();
}
?>
```

<br/>
Appendix I - Extra features
---------------------------

<br/>
**Query overriding**

It is possible to override current query by chaining a call to the *query_callback* method. This method expects a Closure object which receives the query that will be executed right after.

```php
<?php
//composer autoloader
require __DIR__ . "/vendor/autoload.php";

use eMapper\MySQL\MySQLMapper;

$order = 'user_name ASC';
$mapper = new MySQLMapper('my_db', 'localhost', 'my_user', 'my_pass');

//get users
$users = $mapper->map('obj[]')
->query_callback(function ($query) {
    //apply custom order
    return $query . ' ORDER BY ' . $order;
})
->query("SELECT * FROM users");
?>
```

<br/>
**Empty results**

Through the *no_rows* method we can assign the execution of an auxiliary callback whenever an empty result is retrieved from the database. The obtained result is sent as a parameter.

```php
<?php
//composer autoloader
require __DIR__ . "/vendor/autoload.php";

use eMapper\MySQL\MySQLMapper;

$mapper = new MySQLMapper('my_db', 'localhost', 'my_user', 'my_pass');

//get users
$users = $mapper->map('obj[]')
->no_rows(function ($result) {
    die('No users have been found :(');
})
->query("SELECT * FROM users");
?>
```

<br/>
**Non-escaped strings**

Type *ustring* can be used to insert non-escaped strings into a query.

```php
<?php
//composer autoloader
require __DIR__ . "/vendor/autoload.php";

use eMapper\MySQL\MySQLMapper;

$mapper = new MySQLMapper('my_db', 'localhost', 'my_user', 'my_pass');

//get users ordered by id
$users = $mapper->map('obj[]')->query("SELECT * FROM users ORDER BY %{ustring} %{ustring}", 'user_id', 'ASC');
?>
```
If certain string needs to be escaped before being inserted, then we can use the *escape* method available in the MySQLMapper class.

```php
<?php
//composer autoloader
require __DIR__ . "/vendor/autoload.php";

use eMapper\MySQL\MySQLMapper;

$mapper = new MySQLMapper('my_db', 'localhost', 'my_user', 'my_pass');
$search = 'doe';

//find users
$users = $mapper->map('obj[]')->query("SELECT * FROM users WHERE user_name LIKE '%%{us}%'", $mapper->escape($search));
?>
```

<br/>
Appendix II - Configuration options
----------------------------------

<br/>
Configuration options are values that manage a mapper object behavior during a query/statement/procedure execution. They can be configured through class methods like *map*, *cache*, etc. or can be manipulated directly with ***set***. This is the list of all predefined keys available with their respective descriptions.

<br/>
**Database properties**
<table>
    <thead>
        <tr>
            <th>Name</th>
            <th>Type</th>
            <th>Description</th>
            <th>Notes</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>db.name</td>
            <td>string</td>
            <td>Database name.</td>
            <td>
            Initialized during instantiation.
            <br/>
            Evaluated on first query.
            <br/>
            Default value: <em>none</em>.
            </td>
        </tr>
        <tr>
            <td>db.host</td>
            <td>string</td>
            <td>Database host.</td>
            <td>
            Initialized during instantiation.
            <br/>
            Evaluated on first query.
            <br/>
            Default value: <em>none</em>.
            </td>
        </tr>
        <tr>
            <td>db.user</td>
            <td>string</td>
            <td>Database user</td>
            <td>
            Initialized during instantiation.
            <br/>
            Evaluated on first query.
            <br/>
            Default value: <em>none</em>.
            </td>
        </tr>
        <tr>
            <td>db.password</td>
            <td>string</td>
            <td>Database user password.</td>
            <td>
            Initialized during instantiation.
            <br/>
            Evaluated on first query.
            <br/>
            Default value: <em>none</em>.
            </td>
        </tr>
        <tr>
            <td>db.port</td>
            <td>string</td>
            <td>Database port.</td>
            <td>
            Initialized during instantiation.
            <br/>
            Evaluated on first query.
            <br/>
            Default value: <em>none</em>
            </td>
        </tr>
        <tr>
            <td>db.socket</td>
            <td>string</td>
            <td>Database socket.</td>
            <td>
            Initialized during instantiation.
            <br/>
            Evaluated on first query.
            <br/>
            Default value: <em>none</em>.
            </td>
        </tr>
        <tr>
            <td>db.autocommit</td>
            <td>boolean</td>
            <td>Database autocommit feature.</td>
            <td>
            Initialized during instantiation.
            <br/>
            Evaluated on first query.
            <br/>
            Default value: <em>true</em>.
            </td>
        </tr>
        <tr>
            <td>db.prefix</td>
            <td>string</td>
            <td>Database prefix.</td>
            <td>
            <br/>
            Default value: <em>none</em>.
            </td>
        </tr>
    </tbody>
</table>
<br/>
**Mapping**
<table>
    <thead>
        <tr>
            <th>Name</th>
            <th>Type</th>
            <th>Description</th>
            <th>Notes</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>map.type</td>
            <td>string</td>
            <td>Mapping expression.</td>
            <td>
            Initialized with <em>map</em> method.
            <br/>
            Default value: <em>array[]</em>.
            </td>
        </tr>
        <tr>
            <td>map.model</td>
            <td>eMapper\Model\Model</td>
            <td>Mapping model to use.</td>
            <td>
            Initialized with <em>model</em> method .
            <br/>
            Evaluated during array/object result mapping.
            <br/>
            Default value: <em>none</em>.
            </td>
        </tr>
        <tr>
            <td>map.params</td>
            <td>array</td>
            <td>Mapping parameters.</td>
            <td>
            Initialized with <em>map</em> method.
            <br/>
            Default value: <em>none</em>.
            </td>
        </tr>
    </tbody>
</table>
<br/>
**Cache**
<table>
    <thead>
        <tr>
            <th>Name</th>
            <th>Type</th>
            <th>Description</th>
            <th>Notes</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>cache.provider</td>
            <td>eMapper\Cache\CacheProvider</td>
            <td>Cache provider to use.</td>
            <td>
            Initialized with <em>setProvider</em> method.
            <br/>
            Default value: <em>none</em>.
            </td>
        </tr>
        <tr>
            <td>cache.key</td>
            <td>string</td>
            <td>Cache key to use.</td>
            <td>
            Initialized with <em>cache</em> method.
            <br/>
            Default value: <em>none</em>.
            </td>
        </tr>
        <tr>
            <td>cache.ttl</td>
            <td>integer</td>
            <td>Cache TTL (time to live).</td>
            <td>
            Numerical.
            <br/>
            Initialized with <em>cache</em> method.
            <br/>
            Default value: 0.
            </td>
        </tr>
    </tbody>
</table>
<br/>
**Callbacks**
<table>
    <thead>
        <tr>
            <th>Name</th>
            <th>Type</th>
            <th>Description</th>
            <th>Notes</th>
        </tr>
    </thead>
    <tbody>
     <tr>
            <td>callback.each</td>
            <td>callable</td>
            <td>Defines a callback which receives all mapped elements from a query.</td>
            <td>
            Initialized with <em>each</em> method.
            <br/>
            Default value: <em>none</em>.
            </td>
        </tr>
        <tr>
            <td>callback.filter</td>
            <td>callable</td>
            <td>Sets a callback which defines a filter to apply to all elements on a list.</td>
            <td>
            Similar to apply <em>array_filter</em> to a list of rows.
            <br/>
            Initialized with <em>filter</em> method.
            <br/>
            Default value: <em>none</em>.
            </td>
        </tr>
        <tr>
            <td>callback.no_rows</td>
            <td>Closure</td>
            <td>Defines a callback which is called if the given query returns an empty result.</td>
            <td>
            Initialized with <em>no_rows</em> method.
            <br/>
            Can override return value.
            <br/>
            Default value: <em>none</em>.
            </td>
        </tr>
         <tr>
            <td>callback.query</td>
            <td>Closure</td>
            <td>Defines a callback which receives the generated query.</td>
            <td>
            Initialized with <em>query_callback</em> method.
            <br/>
            Can override query to run by returning a value.
            <br/>
            Default value: <em>none</em>.
            </td>
        </tr>
        <tr>
            <td>callback.result</td>
            <td>Closure</td>
            <td>Defines a callback which receives the obtained result.</td>
            <td>
            Initialized with <em>result_callback</em> method.
            <br/>
            Default value: <em>none</em>.
            </td>
         </tr>
         <tr>
            <td>dynamic.*</td>
            <td>Closure</td>
            <td>Defines a Dynamic SQL callback.</td>
            <td>
            These callbacks are invoked from within the query by adding their callback id between double brackets.
            <br/>
            Default value: <em>none</em>
            </td>
        </tr>
    </tbody>
</table>

<br/>
**Stored procedure properties**
<table>
    <thead>
        <tr>
            <th>Name</th>
            <th>Type</th>
            <th>Description</th>
            <th>Notes</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>procedure.use_prefix</td>
            <td>boolean</td>
            <td>Determines whether the procedure name uses the database prefix.</td>
            <td>
            Default value: <em>false</em>.
            </td>
        </tr>
        <tr>
            <td>procedure.types</td>
            <td>array</td>
            <td>Sets procedure parameter types.</td>
            <td>
            Initialized through the <em>ptypes</em> method.
        </tr>
    </tbody>
</table>
<br/>
License
--------------
<br/>
This code is licensed under the BSD 2-Clause license.
