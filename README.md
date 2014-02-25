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

  * Deprecated: Models
  * Added: Support for SQLite and PostgreSQL
  * Added: Result maps
  * Added: Entities
  * Added: Support for Dynamic SQL clauses through eMacros
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
- [eMacros](https://github.com/emaphp/eMacros "") package
 
<br/>
Installation
--------------
<br/>
**Installation through Composer**
<br/>
```javascript
{
    "require": {
        "emapper/emapper" : "dev-master*"
    }
}
```

<br/>
Introduction
------------

<br/>
*eMapper* is a PHP library aimed to provide a simple, powerful and highly customizable data mapping tool. It comes with some interesting features like:

- **Customized mapping**: Results can be mapped to a desired type through mapping expressions.
- **Indexation and Grouping**: Lists can be indexed or grouped together by a column value.
- **Custom types**: Developers can design their own types and custom type handlers.
- **Cache providers**: Obtained data can be stored in cache using APC or Memcache.
- **Dynamic SQL**: Queries can contain Dynamic SQL clauses writted in *eMacros*.


<br/>
First steps
-----------

<br/>
To start developing with *eMapper* we must create an instance of one of the mapping classes available in the library. Currently, *eMapper* supports MySQL, SQLite an PostgreSQL.

<br/>
**MySQL**

<br/>
```php
//composer autoloader
require __DIR__ . "/vendor/autoload.php";

use eMapper\Engine\MySQL\MySQLMapper;

$mapper = new MySQLMapper('my_db', 'localhost', 'my_user', 'my_pass');
```
<table width="95%">
    <thead>
        <tr>
            <th colspan="4">Arguments for MySQLMapper class</th>
        </tr>
        <tr>
            <th>Name</th>
            <th>Type</th>
            <th>Description</th>
            <th>Default value</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>database</td>
            <td>String</td>
            <td>Database name</td>
            <td><em>None</em></td>
        </tr>
        <tr>
            <td>host</td>
            <td>String</td>
            <td>Server host</td>
            <td>mysqli.default_host (php.ini)</td>
        </tr>
        <tr>
            <td>user</td>
            <td>String</td>
            <td>User name</td>
            <td>mysqli.default_user (php.ini)</td>
        </tr>
        <tr>
            <td>password</td>
            <td>String</td>
            <td>User password</td>
            <td>mysqli.default_pw (php.ini)</td>
        </tr>
        <tr>
            <td>port</td>
            <td>String</td>
            <td>Server port</td>
            <td>mysqli.default_port (php.ini)</td>
        </tr>
        <tr>
            <td>socket</td>
            <td>String</td>
            <td>Connection socket</td>
            <td>mysqli.default_socket (php.ini)</td>
        </tr>
        <tr>
            <td>charset</td>
            <td>String</td>
            <td>Client charset (setted with mysqli::set_charset)</td>
            <td>'UTF-8'</td>
        </tr>
        <tr>
            <td>autocommit</td>
            <td>Boolean</td>
            <td>Query autocommit</td>
            <td>TRUE</td>
        </tr>
    </tbody>
</table>

<br/>
**SQLite**

<br/>
```php
//composer autoloader
require __DIR__ . "/vendor/autoload.php";

use eMapper\Engine\SQLite\SQLiteMapper;

$mapper = new SQLiteMapper('company.db');
```

<table width="95%">
    <thead>
        <tr>
            <th colspan="4">Arguments for SQLiteMapper class</th>
        </tr>
        <tr>
            <th>Name</th>
            <th>Type</th>
            <th>Description</th>
            <th>Default value</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>filename</td>
            <td>String</td>
            <td>Database file</td>
            <td><em>None</em></td>
        </tr>
        <tr>
            <td>flags</td>
            <td>Integer</td>
            <td>Connection flags</td>
            <td>SQLITE3_OPEN_READWRITE | SQLITE3_OPEN_CREATE</td>
        </tr>
        <tr>
            <td>encription_key</td>
            <td>String</td>
            <td>Encription key</td>
            <td><em>NULL</em></td>
        </tr>
    </tbody>
</table>

<br/>
**PostgreSQL**

<br/>
```php
//composer autoloader
require __DIR__ . "/vendor/autoload.php";

use eMapper\Engine\PostgreSQL\PostgreSQLMapper;

$mapper = new PostgreSQLMapper('dbname=company user=test password=test');
```
<table width="95%">
    <thead>
        <tr>
            <th colspan="4">Arguments for PostgreSQLMapper class</th>
        </tr>
        <tr>
            <th>Name</th>
            <th>Type</th>
            <th>Description</th>
            <th>Default value</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>connection_string</td>
            <td>String</td>
            <td>Connection string</td>
            <td><em>None</em></td>
        </tr>
        <tr>
            <td>connect_type</td>
            <td>Integer</td>
            <td>Connection type</td>
            <td><em>None</em></td>
        </tr>
    </tbody>
</table>

<br/>
A mapper instance stores these configuration values internally. The connection to the database is not made when creating an instance but right before a query is submitted. In order to check the proper connection to the database we can use the **connect** method.

<br/>
Arrays
-------

<br/>
**Obtain a list of rows as an array**

<br/>
This example shows how we submit a query through the **query** method. The obtained result is then converted to the default type: a list of arrays, with each array having numeric and string indexes.

```php
//get a list of users as an array of arrays
$users = $mapper->query("SELECT * FROM users");

//...

//close connection
$mapper->close();
```

<br/>
**Obtain a row as an associative array**

<br/>
The desired type to obtain from a query is declared through the **type** method. This method receives a mapping expression which indicates the expected type. Applying a desired type requires chaining a call to this method before sending the query. In order to obtain an array from a row we indicate the expected type as *array* (or *arr*). We can also tell which type of array to return through a second argument.

```php
use eMapper\Result\ResultInterface;

//obtain a row as an associative array
$user = $mapper->type('array', ResultInterface::ASSOC)
->query("SELECT * FROM users WHERE user_id = 1");
```

<br/>
Objects
-------

<br/>
**Obtain a row as an object**

<br/>
To obtain a *stdClass* instance from a row we simply set the desired type to *object* (or *obj*).

```php
//get user as object (stdClass)
$user = $mapper->type('object')->query("SELECT * FROM users WHERE user_id = 1");
```

<br/>
**Obtain a row as a custom class object**

<br/>
It is possible to define the object class within the mapping expression. In order to show this feature, we have designed a *User* class within the *Acme* namespace.
```php
namespace Acme;

class User {
    public $user_id;
    public $name;
    public $password;
    public $email;
}
```
An object class must be specified right after the desired type in the following way.

```php
//get user as an Acme\User instance
$user = $mapper->type('obj:Acme\User')
->query("SELECT * FROM users WHERE user_id = 1");
```

<br/>
Scalars
-------

<br/>
**Obtain a column value as a string**

<br/>
Mapping expressions also support simple data types.
```php
//get name of user with id = 1
$name = $mapper->type('string')
->query("SELECT name FROM users WHERE user_id = 1");
```

<br/>
**Obtain a custom column value as an integer**

<br/>
By default, scalars are obtained reading from the row's first column. We can change this behaviour by specifying the column name as a second parameter.

```php
//get id from users with name = 'jdoe'
$id = $mapper->type('int', 'user_id')
->query("SELECT * FROM users WHERE name = 'jdoe'");
```

<br/>
Dates
-----

<br/>
**Obtain a column value as a DateTime instance**

<br/>
The *DateTime* type allow us to obtain instances of the *DateTime* class from a column. This example obtains the value from *sale_date* using a *DateTime* type alias as type specifier.

```php
//get sale date
$sale_date = $mapper->type('dt')->query("SELECT sale_date FROM sales WHERE sale_id = 324");
```

Columns of type *DATETIME*, *TIMESTAMP*, etc. are mapped to instances of *DateTime* automatically.

```php
//get user as array
$user = $mapper->type('arr')->query("SELECT * FROM users WHERE user_id = 2");

//show a formatted version of last_login column
echo $user['last_login']->format('m/d/Y H:i:s');
```

<br/>
Lists
-----

<br/>
**Obtain a list of objects**

We can also get lists of a given type by adding brackets at the end of the mapping expression.

```php
//get users as an object list
$users = $mapper->type('object[]')
->query("SELECT * FROM users ORDER BY user_id ASC");
```

<br/>
**Obtain a list of integers**

<br/>
This syntax is also supported when mapping to scalar types. For example, *integer[]* will return a list of integers.
```php
//get user ids as a list
$ids = $mapper->type('integer[]')->query("SELECT user_id FROM users");
```

<br/>
**Obtain a list of strings from a column**

<br/>
A second argument let us define which column to obtain.

```php
//get user names as a list
$names = $mapper->type('str[]', 'name')->query("SELECT * FROM users");
```

<br/>
Indexed lists
-------------

<br/>
**Obtain a list of objects indexed by column**

<br/>
Lists of arrays/objects can be indexed by a given column by specifying that column name between brackets in the mapping expression. The following code returns an array of objects where each key is the corresponding value of *user_id* for that row.

```php
//get an indexed list of objects
$users = $mapper->type('object[user_id]')
->query("SELECT * FROM users");
```

<br/>
**Obtain a list of arrays indexed by column**

This syntax is supported by the array mapper as well.

````php
use eMapper\Result\ResultInterface;

//get a list of associative arrays indexed by user_id
$users = $mapper->type('array[user_id]', ResultInterface::ASSOC)
->query("SELECT * FROM users");
```

Remember that you can use only columns which are present on the result set. If we want a list of numeric arrays the index column must be specified as an integer.

```php
use eMapper\Result\ResultInterface;

//get a list of numeric arrays indexed by user_id
$users = $mapper->type('array[0]', ResultInterface::NUM)
->query("SELECT * FROM users");
```


<br/>
**Obtain a list of objects indexed by column with a custom type**

<br/>
Index type can be declared by adding a type specifier right after its name. If no type is specified then the one associated with the column is used.

```php
//get a list of users indexed by user_id as a string
$users = $mapper->type('object[user_id:string]')
->query("SELECT * FROM users");
```

<br/>
**Custom indexation**

Indexes can be customized by applying an *index callback* through the *index* method. This callback will receive each mapped row obtained from the result.

```php
//generate index from user's mail
$users = $mapper->type('object[]')->index(function ($user) {
    return strstr($user->email, '@', true);
})->query("SELECT * FROM users");
```

<br/>
Grouping
-------

<br/>
**Grouping by a given column**

<br/>
Grouping allows you to organize a list of results with a common characteristic among several arrays. The required syntax is pretty similar to the one used for indexation. This example shows how to obtain a list of objects grouped by *category*.

```php
//get a list of products grouped by category
$products = $mapper->type('obj<category:string>')
->query("SELECT * FROM products");

//print_r($products['software']);
//print_r($products['hardware']);
//...
```

<br/>
**Indexation + Grouping**

<br/>
Indexation and grouping can be combined to obtain more precise lists.

```php
//get a list of products grouped by category and indexed by id
$products = $mapper->type('array<category>[product_id:int]')
->query("SELECT * FROM products");
```


<br/>
**Custom grouping**

We can also define a *group callback* through the *group* method.

```php
//get a list of products grouped by a custom callback
$products = $mapper->type('obj[product_id]')->group(function ($product) {
    return substr($product->codigo, 0, 3);
})->query("SELECT * FROM products");
```

<br/>
Queries
-------

<br/>
**Passing parameters to a query**

<br/>
When submitting a query we can specify an arbitrary number of arguments. Each one of these arguments can be referenced within the query string with an expression that contains a leading **%** character followed by a type identifier between braces.

```php
//obtain user with id = 1
$user = $mapper->type('obj')->query("SELECT * FROM users WHERE user_id = %{int}", 1);
```

The next example shows how to use type specifiers to generate an insertion query.
```php
//user values
$username = 'jdoe';
$password = sha1('jhon123');
$is_admin = false;
$image = file_get_contents('photo.jpg');

//insert data ('x' is short for 'blob')
$mapper->query("INSERT INTO users (username, password, is_admin, image) 
                VALUES (%{s}, %{s}, %{b}, %{x})",
               $username, $password, $is_admin, $image);
```

<br/>
**Passing arrays as parameters**

<br/>
When passing an array, all values are converted to the specified type and then joined together. This is useful when doing a search using the **IN** clause.
```php
//execute query: SELECT * FROM products WHERE code IN ('MXP412', 'TRY235', 'OFR255')
$products = $mapper->query("SELECT * FROM products WHERE code IN (%{s})", array('MXP412', 'TRY235', 'OFR255'));
```

<br/>
**Specifying parameters by order of appearance**

<br/>
There's an additional syntax that allow us to refer to a parameter by its order of appearance. Instead of the desired type we use the parameter number and (optionally) a type identifier.
```php
//first parameter is %{0}
$products = $mapper->type('obj[product_id]')
->query("SELECT * FROM products
         WHERE product_id = %{1} OR product_code = %{0:s}", 'PHN00098', 3);
```

We can also tell from which subindex must be obtained a value. A subindex must be appended right after the parameter index and placed between brackets.

```php
$param_list = array('id' => 1, 'jdoe', 'david');

$users = $mapper->type('obj[]')
->query("SELECT * FROM users
         WHERE user_id = %{0[id]} OR username = %{0[1]:str} OR username = %{0[2]:str}", $param_list);
```

<br/>
**Ranges**

Ranges allow to specify a subset of a list passed as argument. The obtained expression is equivalent to calling [array_slice](http://www.php.net/manual/en/function.array-slice.php "") with the specified array. The left value represents the offset and the right one the length.
```php
$list = array(45, 23, '43', '164', 43);

//obtain a sublist with '43' and '164'
$users = $mapper->type('obj[]')
->query("SELECT * FROM users WHERE user_id IN (%{0[2..2]:i})", $list);
```

If one value is omitted then the corresponding limit is used:

* [..3] Obtains the first 3 elements.
* [1..] Obtains all elements except the first one.
* [..] Obtains the whole list.

<br/>
These expressions can be used with string values as well.

```php
$name = "XXXjdowXXX";

//get user witn name = 'jdoe'
$user = $mapper->type('obj')
->query("SELECT * FROM users WHERE name = %{0[3..4]}", $name);
```

<br/>
**Using objects and arrays as parameter**

<br/>
Queries also supports a syntax which obtains values from object properties (and array keys). We can refer to an object property with the **#** symbol and the object property between braces. Just like previous mapping expressions, it is also possible to specify the property type, subindex and range.

```php
//user values
$user = new stdClass();
$user->username = 'jdoe';
$user->password = sha1('jhon123');
$user->is_admin = false;
$user->image = file_get_contents('photo.jpg');

//insert data
$mapper->query("INSERT INTO users (username, password, is_admin, image)
                VALUES (#{username}, #{password:s}, #{is_admin}, #{image:blob})", $user);
```

<br/>
**Database prefix**

<br/>
We can store a database prefix with the **setPrefix** method. The expression **@@** can then be used to insert this prefix within a query.

```php
use eMapper\Engine\MySQL\MySQLMapper;

$mapper = new MySQLMapper('project');

//set prefix
$mapper->setPrefix('PRJ_');

//SQL: SELECT * FROM PRJ_items WHERE active = TRUE
$items = $mapper->query("SELECT * FROM @@items WHERE active = TRUE");
```

<br/>
Result maps
----------

<br/>
A result map is a class that defines which properties will be mapped to an object / array. Using a result map is ideal for cases where for some reason the values ​​in a column must be stored using another name or with a particular type. In order to define a property type and the name of the referenced column we use *annotations*. The following code shows the implementation of a result map that defines 4 properties. The **@column** and **@type** annotations are used to define the type to use and the name of the column from which to take the value respectively. If no column is specified then the property name is used. If the type is not defined then the one associated with the column is used.

```php
namespace Acme\Result;

class UserResultMap {
    /**
     * @column user_id
     */
    public $id;
    
    /**
     * @type string
     */
    public $name;
    
    /**
     * @column password
     */
    public $psw;
    
    /**
     * @type blob
     * @column photo
     */
    public $avatar;
}
```
A result map is applied by chaining a call to the **result_map** method with the result map class fullname as argument.

```php
$user = $mapper->type('obj')
->result_map('Acme\Result\UserResultMap')
->query("SELECT * FROM users WHERE user_id = 1");
```

This example returns a *stdClass* instance with the properties *id*, *name*, *psw* and *avatar*.

<br/>
Entities
----------

<br/>
Just like a result map, an entity is a class that defines which properties must be mapped according to the its structure. Unlike a result map, an entity can be used as a mapping expression directly.

```php
namespace Acme\Entity;

/**
 * @entity
 */
class Product {
    /**
     * @column product_id
     */
    public $id;
    
    /**
     * @type str
     */
    public $code;
    
    /**
     * @column modified_at
     * @type string
     */
    public $modifiedAt;
}
```
All entities must be declared using the **@entity** annotation. Mapping to an entity is pretty straightforward.

```php
$products = $mapper->type('obj:Acme\Entity\Product[id]')
->query("SELECT * FROM products");
```

When mapping to an indexed list of entities the specified index must be the property name, not the associated column. Same goes for result maps.

<br/>
Declaring a property as private/protected requires adding the **@getter** and **@setter** annotations.

```php
namespace Amce\Entity;

/**
 * @entity
 */
class Profile {
    /**
     * @column profile_id
     * @setter setProfileId
     * @getter getProfileId
     */
    private $profileId;

    /**
     * @column user_id
     * @setter setUserId
     * @getter getUserId
     */
    private $userId;
    
    /**
     * @type string
     * @setter setType
     * @getter getType
     */
    private $type;
    
    public function setProfileId($profileId) {
        $this->profileId = $profileId;
    }
    
    public function getProfileId() {
        return $this->profileId;
    }
    
    public function setUserId($userId) {
        $this->userId = $userId;
    }
    
    public function getUserId() {
        return $this->userId;
    }
    
    public function setType($type) {
        $this->tipo = $type;
    }
    
    public function getType() {
        return $this->type;
    }
}
```


<br/>
Statements
----------

<br/>
**Creating statements**

<br/>
Statements are objects that store a SQL query and can be identified by a string id. These objects can be declared in 2 ways. The first one consist in creating an instance of the *eMapper\SQL\Statement* class, which later can be added to a mapper instance through the **addStatement** method.

```php
use eMapper\SQL\Statement;

//create statement
$stmt = new Statement('findAllUsers', "SELECT * FROM users");

//store
$mapper->addStatement($stmt);
```
The second one uses the **stmt** method.

```php
//create statement
$mapper->stmt('findAllUsers', "SELECT * FROM users");
```

**stmt** returns a reference to the object where the statement is stored. Multiple calls can be chained in order to declare multiple statements.

```php
//create statements
$mapper
->stmt('findAllUsers', "SELECT * FROM users")
->stmt('findAllProducts', "SELECT * FROM products")
->stmt('findAllSales', "SELECT * FROM sales");
```

<br/>
**Executing statements**

<br/>
Executing a statement is done by invoking the **execute** method and passing the statement identifier as argument. Just like **query**, this method takes an arbitrary number of additional arguments.

```php
//add statement
$mapper->stmt('findUserByPK', "SELECT * FROM users WHERE user_id = %{i}");

//execute statement
$user = $mapper->type('obj')->execute('findUserByPK', 5);
```

<br/>
**Configuration**

<br/>
Both the *Statement* class constructor and the **stmt** method support and additional third parameter that takes a *StatementConfiguration* instance. This argument defines the default behaviour for that statement and can be generated through the **type** and **config** methods in the *Statement* class.

```php
use eMapper\SQL\Statement;

//set default type to object list
$stmt = new Statement('findAllProducts',
                      "SELECT * FROM products",
                      Statement::type('obj[]'));
                      
$mapper->addStatement($stmt);

//set result map and default type
$mapper->stmt('findAllUsers',
              "SELECT * FROM users",
              Statement::config()->result_map('Acme\Result\UserResultMap')->type('obj[id]'));

//execute statements
$products = $mapper->execute('findAllProducts'):
//...
$users = $mapper->execute('findAllUsers');
```
The **type** method is a simplified way to define the expected type. **config**, on the other hand, takes an array of supported options as an argument. *findAllUsers* configuration can be also defined like this.

```php
Statement::config(['map.result' => 'Acme\Result\UserResultMap', 'map.type' => 'obj[id]']);
```

A list of supported configuration values can be found in *Appendix II - Configuration options*.


<br/>
Namespaces
----------

<br/>
**Organizing statements**

<br/>
Namespaces are objects designed with the purpose of storing a list of statements more easily. These can turn really helpful in medium and large projects, where a large number of queries are created and stored. In order to create a namespace we must first build a new instance of the *eMapper\SQL\StatementNamespace* class. This class constructor takes a string id as argument. Once created we can add an arbitrary number of statements using the same method we've seen previously. To store a namespace within a mapper instance we use the **addNamespace** method.

```php
use eMapper\SQL\Statement;
use eMapper\SQL\StatementNamespace;

//crate namespace
$ns = new StatementNamespace('users');

//add statement
$stmt = new Statement('findAll', "SELECT * FROM users");
$ns->addStatement($stmt);

//adding statement through 'stmt'
$ns->stmt('findByPK', "SELECT * FROM users WHERE user_id = %{i}");

//add namespace
$mapper->addNamespace($ns);
```
To execute a statement within a namespace we must specify the namespace identifier along with the statement id. Identifiers need to be separated from each other using the '.' character.

```php
$users = $mapper->type('arr[]')->execute('users.findAll');
//...
$user = $mapper->type('obj')->execute('users.findByPK', 7);
```

<br/>
**Nested namespaces**

<br/>
A namespace can contain other namespaces in case the complexity of the project requires it. Besides the **addNamespace** method we also have **ns**. This method returns a reference to the generated namespace, which is useful for chaining method invocations to **stmt** and define a group of statements quickly.

```php
use eMapper\SQL\Statement;
use eMapper\SQL\StatementNamespace;

//create namespace
$usersNamespace = new StatementNamespace('users');

//nested namespace
$profilesNamespace = new StatementNamespace('profiles');
$profilesNamespace->stmt('findByUserId',
                         "SELECT * FROM profiles WHERE user_id = %{i}",
                         Statement::type('obj[]'));

//add namespace
$usersNamespace->addNamespace($profilesNamespace);

//using the 'ns' method
$usersNamespace->ns('admin')
->stmt('delete', "DELETE FROM users WHERE user_id = %{i}")
->stmt('ban', "UPDATE users SET state = 'banned' WHERE user_id = %{i}");

$mapper->addNamespace($usersNamespace);

//...
$perfil = $mapper->execute('users.profiles.findByUserId', 4);
//...
$mapper->execute('users.admin.ban', 7);
```

<br/>
**Custom namespaces**

<br/>
We can create customized namespaces by extending the *eMapper\SQL\StatementNamespace* class. Customized namespaces need to declare their id by calling the parent class constructor.

```php
namespace Acme\SQL;

use eMapper\SQL\StatementNamespace;
use eMapper\SQL\Statement;

class UsersNamespace extends StatementNamespace {
	public function __construct() {
		parent::__construct('users');
		
		$this->stmt('findByUsername',
		            "SELECT * FROM users WHERE name = %{s}");
		            
		$this->stmt('findByPK',
		            "SELECT * FROM users WHERE user_id = %{i}",
		            Statement::type('obj'));
		            
		$this->stmt('findAll',
		            "SELECT * FROM users",
		            Statement::type('obj[user_id]'));
	}
}
```


<br/>
Stored procedures
-----------------

<br/>
**Calling stored procedures**

<br/>
Stored procedures are database routines aimed to provide persistence logic. These routines can be invoked from a mapper instance through a special feature that translates the invocation of a non-declared method into a stored procedure call. This is accomplished through a language feature called [overloading](http://php.net/manual/en/language.oop5.overloading.php "").

```php
//SQL: CALL FindUserByUsername('jdoe')
$user = $mapper->type('object')->FindUserByUsername('jdoe');
```

<br/>
**Argument types**

<br/>
We can specify parameter types through the **sptypes** method when needed. Each one of the arguments corresponds to a parameter type. The code below calls a stored procedure specifying the argument types (username:string, password:string, is_admin:boolean).
```php
//SQL: CALL InsertNewUser('juana', 'clave123', TRUE)
$id_usuario = $mapper->type('int')
->sptypes('s', 's', 'b')
->InsertNewUser('juana', 'clave123', 1);
```

<br/>
**Database prefix**

<br/>
It is common to declare stored procedures using the database prefix. By default, whenever a stored procedure is being called, the database prefix is appended in front of its name. This behaviour can be modified by calling the **usePrefix** method.

```php
use eMapper\MySQL\MySQLMapper;

$mapper = new MySQLMapper('my_db', 'localhost', 'my_user', 'my_pass');

//set database prefix
$mapper->setPrefix('EMP_');

//add prefix
$mapper->usePrefix(true);

//SQL: CALL EMP_InsertImage('My Image', x...);
$id_imagen = $mapper->type('integer')
->sptypes('string', 'blob')
->InsertImage('My Image', file_get_contents('image.png'));
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
Excepciones
----------

<br/>
Altrough *eMapper* uses the exceptions already included in SPL, there are some special scenarios where those included in the library are used. These special scenarios are the following:

 - Query syntax error
 - Connection to database server failed
 - Errors found when processing the returned result

<br/>
The associated exception with each one of these scenarios depends on the database server currently used, althrough all of them extend *eMapper\Exception\MapperException*.

<br/>
**MySQL**

```php
use eMapper\Engine\MySQL\MySQLMapper;
use eMapper\Engine\MySQL\Exception\MySQLMapperException;
use eMapper\Engine\MySQL\Exception\MySQLQueryException;
use eMapper\Engine\MySQL\Exception\MySQLConnectionException;

$mapper = new MySQLMapper('db');

try {
    $mapper->query($query);
}
catch (MySQLQueryException $qe) {
    echo 'Query error! ' . $qe->getMessage();
    echo '<br/>Tried to execute ' . $qe->getQuery();
}
catch (MySQLConnectionException $ce) {
    echo 'Connection failed! ' . $ce->getMessage();
}
catch (MySQLMapperException $me) {
    echo 'Error! ';
    
    if ($me->getPrevious() != null) {
        echo $me->getPrevious()->getMessage();
    }
    else {
        echo $me->getMessage();
    }
}
```

<br/>
**SQLite**

```php
use eMapper\Engine\SQLite\SQLiteMapper;
use eMapper\Engine\SQLite\Exception\SQLiteMapperException;
use eMapper\Engine\SQLite\Exception\SQLiteQueryException;
use eMapper\Engine\SQLite\Exception\SQLiteConnectionException;

$mapper = new SQLiteMapper('filename.db');

try {
    $mapper->query($query);
}
catch (SQLiteQueryException $qe) {
    echo 'Query error! ' . $qe->getMessage();
    echo '<br/>Tried to execute ' . $qe->getQuery();
}
catch (SQLiteConnectionException $ce) {
    echo 'Connection failed! ' . $ce->getMessage();
}
catch (SQLiteMapperException $me) {
    echo 'Error! ';
    
    if ($me->getPrevious() != null) {
        echo $me->getPrevious()->getMessage();
    }
    else {
        echo $me->getMessage();
    }
}
```

<br/>
**PostgreSQL**

```php
use eMapper\Engine\PostgreSQL\PostgreSQLMapper;
use eMapper\Engine\PostgreSQL\Exception\PostgreSQLMapperException;
use eMapper\Engine\PostgreSQL\Exception\PostgreSQLQueryException;
use eMapper\Engine\PostgreSQL\Exception\PostgreSQLConnectionException;

$mapper = new PostgreSQLMapper('dbname=test');

try {
    $mapper->query($query);
}
catch (PostgreSQLQueryException $qe) {
   echo 'Query error! ' . $qe->getMessage();
    echo '<br/>Tried to execute ' . $qe->getQuery();
}
catch (PostgreSQLConnectionException $ce) {
    echo 'Connection failed! ' . $ce->getMessage();
}
catch (PostgreSQLMapperException $me) {
    echo 'Error! ';
    
    if ($me->getPrevious() != null) {
        echo $me->getPrevious()->getMessage();
    }
    else {
        echo $me->getMessage();
    }
}
```

<br/>
Appendix I - Additional features
---------------------------

<br/>
**Raw results**

<br/>
Through the **sql** method we can query the database to obtain the result without further processing. Result type will depend  on the mapper class that we are using.

```php
//call sql method
$result = $mapper->sql("SELECT user_id, name FROM users WHERE user_id = %{i}", 5);

//mysql
while (($row = $result->fetch_array()) != null) {
    //...
}

//free result
$mapper->free_result($result);
```

<br/>
**Query overriding**

The *query_override* method allows us to rewrite the query which is sent to the database server according to a certain logic. This method argument is a function whose first parameter is the query to perform. By returning a value we can override the query that will be finally sent.

```php
//composer autoloader
require __DIR__ . "/vendor/autoload.php";

use eMapper\Engine\MySQL\MySQLMapper;

$order = 'user_name ASC';
$mapper = new MySQLMapper('my_db', 'localhost', 'my_user', 'my_pass');

$users = $mapper
->type('obj[]')
->query_override(function ($query) use ($order) {
    //apply order
    return $query . ' ORDER BY ' . $order;
})
->query("SELECT * FROM users");
```

<br/>
**Empty results**

Through the **no_rows** method we can associate the execution of an auxiliary function whenever a query does not return any rows. This function takes as argument the obtained result.

```php
use eMapper\Engine\MySQL\MySQLMapper;

$mapper = new MySQLMapper('my_db', 'localhost', 'my_user', 'my_pass');

//get users
$users = $mapper->type('obj[]')
->no_rows(function ($result) {
    throw new \UnexpectedValueException('No users found :(');
})
->query("SELECT * FROM users");
```

<br/>
**The 'each' method**

<br/>
The **each** method allows us to apply a user-defined function to each of the rows returned by a query. This function will receive two arguments: the row corresponding to the current value and the current mapper instance. The example below calculates the age of each user returned by a query and stores it within the *age* property.

```php
//get users
$users = $mapper->each(function (&$user, $mapper) {
    $user->age = (int) $user->birth_date->diff(new \DateTime())->format('%y');
})->query("SELECT user_id, name, birth_date FROM users LIMIT 10");

```
<br/>
**Filters**

<br/>
The **filter** method applies a user-defined filter function which removes those rows that do not meet certain condition. Using a filter is similar to set a user-defined function with [array_filter](http://www.php.net/manual/en/function.array-filter.php "").


```php
//remove users without photo
$users = $mapper->filter(function ($user) {
    return isset($user->photo);
})->execute('users.findAll');
```
Whenever is applied to a single element (which do not come within a list) and the condition evaluates to false then the retured value is NULL.

<br/>
**Unescaped strings**

The *ustring* type lets you insert an unescaped string within a query. This feature requires special attention because it is possible to suffer from *SQL injection attacks* if not used properly.

```php
//composer autoloader
require __DIR__ . "/vendor/autoload.php";

use eMapper\Engine\MySQL\MySQLMapper;

$mapper = new MySQLMapper('my_db', 'localhost', 'my_user', 'my_pass');

//get users
$users = $mapper
->type('obj[]')
->query("SELECT * FROM users ORDER BY %{ustring} %{ustring}", 'user_id', 'ASC');
```



<br/>
Appendix II - Configuration options
----------------------------------

<br/>
Configuration options are values that manage a mapper object behavior during a query/statement/procedure execution. They can be configured through class methods like *type*, *cache*, etc. or can be manipulated directly with ***set***. This is the list of all predefined keys available with their respective descriptions.

<br/>
**MySQL**

<table width="95%">
    <thead>
        <tr>
            <th>Name</th>
            <th>Type</th>
            <th>Description</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>db.name</td>
            <td>String</td>
            <td>Database name</td>
        </tr>
        <tr>
            <td>db.host</td>
            <td>String</td>
            <td>Host name</td>
        </tr>
        <tr>
            <td>db.user</td>
            <td>String</td>
            <td>User name</td>
        </tr>
        <tr>
            <td>db.password</td>
            <td>String</td>
            <td>User password</td>
        </tr>
        <tr>
            <td>db.port</td>
            <td>String</td>
            <td>Server port</td>
        </tr>
        <tr>
            <td>db.socket</td>
            <td>String</td>
            <td>Server socket</td>
        </tr>
        <tr>
            <td>db.charset</td>
            <td>String</td>
            <td>Client charset (setted with mysqli::set_charset)</td>
        </tr>
        <tr>
            <td>db.autocommit</td>
            <td>Boolean</td>
            <td>Autocommit option (setted with mysqli::autocommit)</td>
        </tr>
    </tbody>
</table>

<br/>
**SQLite**

<table width="95%">
    <thead>
        <tr>
            <th>Name</th>
            <th>Type</th>
            <th>Description</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>db.filename</td>
            <td>String</td>
            <td>Database filename</td>
        </tr>
        <tr>
            <td>db.flags</td>
            <td>Integer</td>
            <td>Connection flags (default to SQLITE3_OPEN_READWRITE | SQLITE3_OPEN_CREATE)</td>
        </tr>
            <td>db.encription_key</td>
            <td>String</td>
            <td>Encription key</td>
        </tr>
    </tbody>
</table>

<br/>
**PostgreSQL**
<table width="95%">
    <thead>
        <tr>
            <th>Name</th>
            <th>Type</th>
            <th>Description</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>db.connection_string</td>
            <td>String</td>
            <td>Database connection string</td>
        </tr>
        <tr>
            <td>db.connection_type</td>
            <td>Integer</td>
            <td>Connection type</td>
        </tr>
    </tbody>
</table>

<br/>
**Generic**
<table width="95%">
    <thead>
       <tr>
            <th>Name</th>
            <th>Type</th>
            <th>Description</th>
            <th>Default value</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>db.prefix</td>
            <td>String</td>
            <td>Database prefix</td>
            <td><em>Empty string</em></td>
        </tr>
    <tbody>
</table>

<br/>
**Data mapping**

<table width="95%">
    <thead>
        <tr>
            <th>Name</th>
            <th>Type</th>
            <th>Description</th>
            <th>Default value</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>map.type</td>
            <td>String</td>
            <td>Mapping expression</td>
            <td><em>None</em></td>
        </tr>
        <tr>
            <td>map.params</td>
            <td>Array</td>
            <td>Additional mapping parameters (for array mapping)</td>
            <td><em>None</em></td>
        </tr>
        <tr>
            <td>map.result</td>
            <td>String</td>
            <td>Result map class fullname</td>
            <td><em>None</em></td>
        </tr>
        <tr>
            <td>map.parameter</td>
            <td>String</td>
            <td>Parameter map class fullname</td>
            <td><em>None</em></td>
        </tr>
    <tbody>
</table>

<br/>
**Cache**
<table width="95%">
    <thead>
        <tr>
            <th>Name</th>
            <th>Type</th>
            <th>Description</th>
            <th>Default value</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>cache.key</td>
            <td>String</td>
            <td>Value identifier</td>
            <td><em>None</em></td>
        </tr>
        <tr>
            <td>cache.ttl</td>
            <td>Integer</td>
            <td>TTL (time to live)</td>
            <td><em>None</em> (0 when no specified)</td>
        </tr>
    </tbody>
</table>

<br/>
**Callbacks**
<table width="95%">
    <thead>
        <tr>
            <th>Name</th>
            <th>Type</th>
            <th>Description</th>
            <th>Default value</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>callback.each</td>
            <td>Callable</td>
            <td>Iteration callback</td>
            <td><em>None</em></td>
        </tr>
        <tr>
            <td>callback.filter</td>
            <td>Callable</td>
            <td>Filtering callback</td>
            <td><em>None</em></td>
        </tr>
        <tr>
            <td>callback.no_rows</td>
            <td>Callable</td>
            <td>Callback to invoke whenever an empty result is returned</td>
            <td><em>None</em></td>
        </tr>
         <tr>
            <td>callback.query</td>
            <td>Callable</td>
            <td>Query overriding callback</td>
            <td><em>None</em></td>
        </tr>
        <tr>
            <td>callback.index</td>
            <td>Callback</td>
            <td>Indexation callback</td>
            <td><em>None</em></td>
         </tr>
         <tr>
            <td>callback.group</td>
            <td>Callable</td>
            <td>Grouping callback</td>
            <td><em>None</em></td>
        </tr>
    </tbody>
</table>

<br/>
**Stored procedures**
<table width="95%">
    <thead>
        <tr>
            <th>Name</th>
            <th>Type</th>
            <th>Description</th>
            <th>Default value</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>procedure.use_prefix</td>
            <td>Boolean</td>
            <td>Determines whether the database prefix is used when invoking stored procedures</td>
            <td>TRUE</td>
        </tr>
        <tr>
            <td>procedure.types</td>
            <td>Array</td>
            <td>Defines the type associated with each argument in a call to a stored procedure</td>
            <td><em>None</em></td>
        </tr>
    </tbody>
</table>

<br/>
**Execution environment**
<table width="95%">
    <thead>
       <tr>
            <th>Name</th>
            <th>Type</th>
            <th>Description</th>
            <th>Default value</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>environment.id</td>
            <td>String</td>
            <td>Execution environment id</td>
            <td>'default'</td>
        </tr>
        <tr>
            <td>environment.class</td>
            <td>String</td>
            <td>Execution environment full class name</td>
            <td>'eMapper\Dynamic\Environment\DynamicSQLEnvironment'</td>
        </tr>
    </tbody>
</table>


<br/>
**Internals**
<table width="95%">
    <thead>
        <tr>
            <th>Name</th>
            <th>Type</th>
            <th>Description</th>
            <th>Default value</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>depth.limit</td>
            <td>Integer</td>
            <td>Relationship nesting limit</td>
            <td>1</td>
        </tr>
        <tr>
            <td>depth.current</td>
            <td>Integer</td>
            <td>Current nesting depth</td>
            <td>0</td>
        </tr>
    </tbody>
</table>

<br/>
License
--------------
<br/>
This code is licensed under the BSD 2-Clause license.
