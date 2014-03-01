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
2014-03-01 - Version 3.0.0

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
        "emapper/emapper" : "dev-master"
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
A mapper instance stores these configuration values internally. Database connection is not stablished after calling the constructor method but right before a query is submitted. In order to test a connection without sending a query we must use the **connect** method.

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
The expected type to obtain from a query is declared through the **type** method. This method receives a string which acts as a mapping expression. Mapping expressions are strings that indicate how a result must be interpreted. Applying a mapping expression requires chaining a call to this method before sending the query. In order to obtain an array from a row we indicate the expected type as *array* (or *arr*). We can also tell which type of array to return by adding a second argument.

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
It is possible to define the object class within the mapping expression. In order to show this feature, we have designed a *User* class in the *Acme* namespace.
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
**Obtain an integer from a custom column**

<br/>
The column can also be specified as an auxilary argument.

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
The *DateTime* type can be used to obtain a *DateTime* instance from a row. This example returns a date using *dt* as a type identifier, which is in fact a *DateTime* alias.

```php
//get sale date
$sale_date = $mapper->type('dt')
->query("SELECT sale_date FROM sales WHERE sale_id = 324");
```

Columns of type *DATETIME*, *TIMESTAMP*, etc. are mapped to instances of *DateTime* automatically.

```php
//get user as array
$user = $mapper->type('arr')
->query("SELECT * FROM users WHERE user_id = 2");

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
//get users as list of objects
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
A second argument can be used to define the column to read.

```php
//get names as a list
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

Remember that you can use only columns that are present in the result set. If we want a list of numeric arrays the index column must be specified as an integer.

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
Grouping allows you to organize a list of rows with a common characteristic among several arrays. The required syntax is pretty similar to the one used for indexation. This example shows how to obtain a list of objects grouped by *category*.

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
    return substr($product->code, 0, 3);
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
$values = ['MXP412', 'TRY235', 'OFR255'];
$products = $mapper->query("SELECT * FROM products WHERE code IN (%{s})", $values);
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
         WHERE user_id = %{0[id]}
         OR username = %{0[1]:str}
         OR username = %{0[2]:str}", $param_list);
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
Queries also supports a syntax which obtains values from object properties (and array keys). We can refer to an object property by putting the property name between braces right after a **#** symbol. Just like previous mapping expressions, it is also possible to specify the property type, subindex and range.

```php
//user values
$user = new stdClass();
$user->username = 'jdoe';
$user->password = sha1('jhon123');
$user->is_admin = false;
$user->img = file_get_contents('photo.jpg');

//insert data
$mapper->query("INSERT INTO users (username, password, is_admin, image)
                VALUES (#{username}, #{password:s}, #{is_admin}, #{img:blob})", $user);
```

<br/>
**Database prefix**

<br/>
We can define a database prefix for the current connection with the **setPrefix** method. The expression **@@** can then be used to insert this prefix within a query.

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
A result map is a class that defines which properties will be mapped to an object / array. Using a result map is ideal for cases where for some reason the values ​​in a column must be stored using another name or with a particular type. In order to define a property type and the name of the referenced column we use *annotations*. The following code shows the implementation of a result map that defines 4 properties. The **@column** and **@type** annotations are used to define the associated type and the column name respectively. If no column name is specified then the mapper assumes that is the same as the property. If the type is not defined then the one associated with the column is used.

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
A namespace can contain other namespaces in case the complexity of the project requires it. Along with the **addNamespace** method we also have **ns**. This method returns a reference to the generated namespace, which is useful for chaining method invocations to **stmt** and define a group of statements quickly.

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
$profile = $mapper->execute('users.profiles.findByUserId', 4);
//...
$mapper->execute('users.admin.ban', 7);
```

<br/>
**Custom namespaces**

<br/>
We can create customized namespaces by extending the *eMapper\SQL\StatementNamespace* class. Customized namespaces need to declare their id by calling their parent class constructor.

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
$user_id = $mapper->type('int')
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
$image_id = $mapper->type('integer')
->sptypes('string', 'blob')
->InsertImage('My Image', file_get_contents('image.png'));
```

<br/>
Configuration
--------------

<br/>
A mapper instance keeps all configuration values defined by the user in an internal array called *config*. Whenever a particular method is called (**result_map** or **type**, for example) a new mapper instance is created. This new instance is a clone of the original with a new configuration value assigned. As a result, the original instance is not modified so it can continue to be used within the script. The example below uses the method **option** to generate a new mapper instance. This new instance will hold a new value called *map.type* with the value *integer*. This configuration key determines the desired result type to obtain from a query. For a more detailed list of the supported configuration options refer to *Appendix II - Configuration options*.

```php
use eMapper\Engine\MySQL\MySQLMapper;

$mapper = new MySQLMapper('my_db', 'localhost', 'my_user', 'my_pass');
$four = $mapper->option('map.type', 'integer')->query("SELECT 2 + 2");
```
<br/>
To store values on the original instance we use the method **set**. This method receives 2 arguments: the configuration key and its value. These values can be then obtained through the **get** method.

```php
use eMapper\Engine\MySQL\MySQLMapper;

$mapper = new MySQLMapper('my_db', 'localhost', 'my_user', 'my_pass');
$mapper->set('foo', 'bar');
$bar = $mapper->get('foo');
```
<br/>
Configuration values can also be referenced from within a query. We do this by surrounding the configuration key between braces after a leading **@** character.

```php
use eMapper\Engine\MySQL\MySQLMapper;

$mapper = new MySQLMapper('my_db', 'localhost', 'my_user', 'my_pass');
$mapper->set('table', 'users');
$user = $mapper->type('obj')->query("SELECT * FROM @{table} WHERE user_id = 2");
```
If a given configuration value cannot be converted to string the expression is left blank.

<br/>
Dynamic SQL
-----------

<br/>
Dynamic SQL is a special feature that allows us to add logic within a query so that its structure can be modified according to the supplied arguments. This capacity for self-modification is achieved by using a language based on [eMacros](https://github.com/emaphp/eMacros ""). The following example illustrates a query that uses one of these expressions to insert a search condition.

```php
$user = $mapper->type('obj')
->query("SELECT * FROM users
         WHERE [[ (if (int? (%0)) 'user_id = %{i}' 'name = %{s}') ]]", 5);
```
Dynamic expressions are enclosed within double brackets. The sample includes a small program that inserts a search condition depending on the type of the first argument.

```lisp
(if (int? (%0)) "user_id = %{i}" "name = %{s}")
```
The *if* function evaluates a condition and returns one of the two supplied arguments depending on whether is true or false. The *int?* macro verifies if the given argument is an integer. In this case, the supplied argument is the one returned by the *%0* macro, which is in fact the first query argument. If the argument happens to be an integer the inserted string will be *'user_id = %{i}'*. Otherwise, the search condition will contain *'name = %{s}'*. Given 5 as argument, this query will return a *stdClass* intance containing all values from the user with ID 5. This other example will evaluate *int?* to false, thus, doing a search by name.

```php
$user = $mapper->type('obj')
->query("SELECT * FROM usuarios
         WHERE [[ (if (int? (%0)) 'user_id = %{i}' 'name = %{s}') ]]", 'jdoe');
```

<br/>
**Syntax differences with eMacros**

<br/>
The dialect used for dynamic expressions is slightly different from eMacros. These differences are minor but important.

 - **Configuration values**: Functions headed by the character **@** can obtain configuration values ​​and determine their existence.

```php
//store column
$mapper->set('order.column', 'product_id');

//(@order.column) => 'product_id'
//(@order.type?) => false
$products = $mapper->type('obj[]')
->query("SELECT * FROM products
         ORDER BY [[ (@order.column) ]] [[ (if (@order.type?) (@order.type) 'DESC') ]]");
```
 - **Non included functions**: Most PHP functions are not declared in the default execution environment. Each time a non included function is called the environment checks for its existence in the PHP environment. If it is found, the environment will try to invoke it with the supplied arguments. Some functions that are not included but can be called within an expresion are **count**, **str_replace**, **nl2br**, etc. Import functions (like *use*) and package functions are not included. Neither are output functions like **echo**, **var-dump** and **print-r**. Same with class/object functions.
 - **Included packages**: The default execution environment includes the **DatePackage** and **RegexPackage** packages. Including more packages will require creating a custom environment.


<br/>
**Typified expressions**

<br/>
Typified expressions are dynamic expression that are evaluated and then converted to a specified type. These expressions are surrounded inside double braces. The following example shows a typified expression which builds a search criteria.

```php
$user = $mapper->type('obj')
->query("SELECT * FROM users WHERE name LIKE {{ (. '%' (%0) '%') }}", 'doe');
```
The type must be specified at the beginning, just after the opening braces. If no type is declared, the value is converted to string.

```php
$products = $mapper->query("SELECT * FROM products WHERE price > {{:int (/ (%0) 2) }}", 45);
```

<br/>
**Execution environments**

<br/>
An execution environment is a class that defines which functions can be invoked within a dynamic expression. These environments can be identified with a string ID (default environment has the ID *default*). You can define the environment of a mapper instance through the **setEnvironment** method. This examples generates 2 mapper instances, each one with a separate environment.

```php
use eMapper\Engine\MySQL\MySQLMapper;
use eMapper\Engine\SQLite\SQLiteMapper;

$mysql = new MySQLMapper('database');
$mysql->setEnvironment('mysql_env');

//...

$sqlite = new SQLiteMapper('database.db');
$sqlite->setEnvironment('sqlite_env');
```

<br/>
**Custom environments**

<br/>
The **setEnvironment** method accepts a second argument with the execution environment class to use (*eMapper\Dynamic\Enviroment\DynamicSQLEnvironment* by default). To build a custom execution environment we can extend this class or *eMapper\Environment\Environment*. The following example shows a custom environment that includes the **StringPackage** and **ArrayPackage** packages.

```php
namespace Acme\SQL\Environment;

use eMacros\Environment\Environment;
use eMapper\Configuration\Configuration;
use eMacros\Package\RegexPackage;
use eMacros\Package\DatePackage;
use eMacros\Package\ArrayPackagePackage;
use eMacros\Package\StringPackage;
use eMapper\Dynamic\Package\CorePackage;

class CustomSQLEnvironment extends Environment {
    use Configuration;
    
    public function __construct() {
		$this->import(new RegexPackage());
		$this->import(new DatePackage());
		$this->import(new ArrayPackage());
		$this->import(new StringPackage());
		$this->import(new CorePackage());
	}
}
```
The *Configuration* trait allows the environment to store configuration values defined in the mapper instance. Notice that the **CorePackage** class is not the one included in *eMacros*. It is important to include this package because otherwise the property access functions will not work quite well with entities.

```php
use eMapper\Engine\MySQL\MySQLMapper;

$mysql = new MySQLMapper('database');
$mysql->setEnvironment('mysql_env', 'Acme\SQL\Environment\CustomSQLEnvironment');
```

<br/>
Cache
-----

<br/>
**Using cache providers**

<br/>
Cache providers provide a generic way to store and retrieve values using libraries like **APC**, **Memcache** and **Memcached**. Using a *cache provider* requires calling the **setCacheProvider** method with a provider instance as argument. The following example initializes a *MySQLMapper* object and then sets a *APCProvider* instance as cache provider.

```php
use eMapper\Engine\MySQL\MySQLMapper;
use eMapper\Cache\APCProvider;

$mapper = new MySQLMapper('my_db', 'localhost', 'my_user', 'my_pass');

//set provider
$mapper->setCacheProvider(new APCProvider());
```

Inserting a retrieving values ​​to and from cache is done by chaining a call to the **cache** method. This method expects a string identifier as first argument. We can also define its TTL (time to live) as a second argument.

```php
//store user list as 'USERS_ALL' for 5 minutes
$users = $mapper->cache('USERS_ALL', 300)
->query("SELECT * FROM users");
```

When using a cache provider the behaviour of the mapper object is modified in the following way:

 - If a value with the given key is found in cache then that value is returned.
 - If no value is found, ***query*** is executed as usual. If the query returns 1 or more rows then the result is mapped and stored in cache. That value is then returned.

<br/>
This example uses Memcache instead of APC and also illustrates how to create a **dynamic cache key**. The parameters used to run the query are also used to build the cache key. As a result, the returned value will be stored using the key *USER_6*.

```php
use eMapper\Engine\MySQL\MySQLMapper;
use eMapper\Cache\MemcacheProvider;

$mapper = new MySQLMapper('my_db', 'localhost', 'my_user', 'my_pass');

//set provider
$mapper->setCacheProvider(new MemcacheProvider('65.132.12.4', 13412));

//get user and store in cache
$user = $mapper->type('array')
->cache('USER_%{i}', 120)
->query("SELECT * FROM users WHERE user_id = %{i}", 6);
```

<br/>
Cumtom type handlers
--------------------

<br/>
*eMapper* allows to associate the value of a given column to a type handler created by the user. To add a user-defined type is necessary to implement a type handler that extends the *eMapper\Type\TypeHandler* class. Our handler must then implement the **setParameter** and **getValue** methods, which insert a value ​​into a query and read from a column respectively. Next examples will introduce an user-defined type handler designed to store and retrieve instances of the *Acme\RGBColor* class. This class represents a RGB color, holding the red, green and blue components.

```php
namespace Acme\Type;

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
**The RGBColorTypeHandler class**

<br/>
Our type handler is responsible for converting an instance of *RGBColor* to its corresponding hexadecimal representation. Then, take that representation from the database and generate a *RGBColor* instance again.

```php
namespace Acme\Type;

use eMapper\Type\TypeHandler;

class RGBColorTypeHandler extends TypeHandler {
	/**
	 * Converts a RGBColor instance to its hexadecimal representation
	 */
	public function setParameter($parameter) {
	    //red
	    if ($parameter->red < 16) {
	        $hexred = '0' . dechex($parameter->red);
	    }
	    else {
    	    $hexred = dechex($parameter->red % 256);
	    }
	    
        //green
	    if ($parameter->green < 16) {
	        $hexgreen = '0' . dechex($parameter->green);
	    }
	    else {
    	    $hexgreen = dechex($parameter->green % 256);
	    }
	    
        //blue
	    if ($parameter->blue < 16) {
	        $hexblue = '0' . dechex($parameter->blue);
	    }
	    else {
    	    $hexblue = dechex($parameter->blue % 256);
	    }
	    
		return $hexred . $hexgreen . $hexblue;
	}

	/**
	 * Generates a new RGBColor instance from an hexadecimal representation
	 */
	public function getValue($value) {
		return new RGBColor(hexdec(substr($value, 0, 2)),
		                    hexdec(substr($value, 2, 2)),
		                    hexdec(substr($value, 4, 2)));
	}
}
```

<br/>
**Adding a custom type**

<br/>
User-defined types are added through the **addType** method. This method expects the type class name and a type handler instance. A third parameter can also be added to define a *type alias*.

```php
use Acme\RGBColorTypeHandler;

$mapper->addType('Acme\RGBColor', new RGBColorTypeHandler(), 'color');
```
<br/>
*color* can be used as a type identifier in replacement of *Acme\RGBColor*.

```php
use Acme\Type\RGBColor;

$color = new \stdClass;
$color->name = 'red';
$color->rgb = new RGBColor(255, 0, 0);

$mapper->query("INSERT INTO palette (name, rgb) VALUES (#{name}, #{rgb:color})", $color);
```

<br/>
Both *Acme\RGBColor* and *color* can also be used as mapping expressions when chaining a call to **type**.

```php
$red = $mapper->type('color')->query("SELECT rgb FROM palette WHERE name = 'red'");
```

<br/>
**Setting parameters in a query**

<br/>
By default, if the **setParameter** method returns a string, then that value will be escaped and inserted between quotes. Sometimes this value should not go through this process but inserted directly. This is the case of the *BlobTypeHandler* class, which returns two possible values ('TRUE' or 'FALSE') depending on the parameter. For these type of scenarios, the annotation **@unquoted** was introduced. This annotation, when added to a type handler class, indicates that the value returned by **setParameter** will not be inserted between quotes.

```php
namespace eMapper\Type\Handler;

use eMapper\Type\TypeHandler;

/**
 * @unquoted
 */
class BooleanTypeHandler extends TypeHandler {
	protected function isTrue($value) {
		if (is_string($value) && (strtolower($value) == 'f' || strtolower($value) == 'false')) {
			return false;
		}
	
		return (bool) $value;
	}
	
	public function getValue($value) {
		return $this->isTrue($value);
	}
	
	public function castParameter($parameter) {
		return $this->isTrue($parameter);
	}
	
	public function setParameter($parameter) {
		return ($parameter) ? 'TRUE' : 'FALSE';
	}
}
```
The **castParameter** method is an internal method that is invoked in order to check a parameter type prior to being inserted. This method can be used to notify that a particular value is invalid or generate a valid expression from it.

<br/>
Dynamic attributes
----------


<br/>
A dynamic attribute is a property declared within an entity or result map which is associated with the value returned by a query. These attributes must be defined using a special type of annotations and can define their mapping expressions using **@type**.

<br/>
**Adding a dynamic attribute**

<br/>
To associate a property with the execution of a query we use the **@query** annotation.
```php
namespace Acme\Entity;

/**
 * @entity
 */
class User {
    /**
     * @column user_id
     */
    public $id;
    
    /**
     * @type string
     */
     public $name;
    
    /**
     * @query "SELECT * FROM profiles WHERE user_id = #{id} ORDER BY type"
     * @type obj[]
     */
    public $profiles;
}
```
The specified query receives the current instance of *User* and obtains the associated profiles by its *id*. The result of this query is then mapped to an array of objects.

<br/>
**Parameters**

<br/>
We can specify a variable number of arguments for a given query through the **@arg** annotation. Suppose that we want to filter all associated profiles by type. In order to do this we append an auxilary argument for this query holding the value 1. Since we still need the *User* instance, we must add a special annotation to treat the current user as an argument too.

```php
namespace Acme\Entity;

/**
 * @entity
 */
class User {
    /**
     * @column user_id
     */
    public $id;
    
    /**
     * @type string
     */
     public $name;
    
    /**
     * @query "SELECT * FROM profiles WHERE user_id = #{id} AND type = %{i}"
     * @arg-self
     * @arg 1
     * @type obj[]
     */
    public $profiles;
}
```
**@arg-self** specifies that the current *User* instance must be used as an argument. It is not necessary to add this annotation if no auxiliary arguments are used.

<br/>
**Properties as arguments**

<br/>
It is more likely that the query returning all user profiles was previously declared as a statement.

```php
use eMapper\SQL\StatementNamespace;
use eMapper\SQL\Statement;

$profilesNamespace = new StatementNamespace('profiles');
$profilesNamespace->stmt('findByUserIdAndType',
                         "SELECT * FROM profiles WHERE user_id = #{i} AND type = %{i}",
                         Statement::type('obj[profile_id]'));
                         
$mapper->addNamespace($profilesNamespace);
```
To execute an statement we use the **@stmt** annotation. This particular statement we need to call does not expect a *User* instance as argument but an integer. In order to pass an object property as an argument we must use a special expression.

```php
namespace Acme\Entity;

/**
 * @entity
 */
class User {
    /**
     * @column user_id
     */
    public $id;
    
    /**
     * @type string
     */
     public $name;
    
    /**
     * @stmt 'profiles.findByUserIdAndType'
     * @arg #id
     * @arg 1
     */
    public $perfiles;
}
```
Using a property as an argument requires adding an **@arg** annotation starting with a **#** symbol followed by the property name.

<br/>
**Stored procedures**

<br/>
Calling a stored procedure is performed through the **@procedure** annotation. Unlike **@query** and **@stmt**, stored procedures don't use the current instance as an argument.

```php
namespace Acme\Entity;

/**
 * @entity
 */
class User {
    /**
     * @column user_id
     */
    public $id;
    
    /**
     * @type string
     */
     public $name;
    
    /**
     * @procedure Profiles_FindByUserIdType
     * @arg #id:int
     * @arg 1
     * @type obj[]
     * @result-map Acme\Result\ProfileResultMap
     */
    public $profiles;
}
```
This example introduces the **@result-map** annotation, which indicates the result map class to use for mapping. A type identifier has also been added to the first argument to indicate its type. These expressions can be used to indicate the procedure argument types, just like when calling **sptypes**.

<br/>
**Macros**

<br/>
The **@eval** annotation allows to associate a property to the value returned by a user macro. This annotation uses the same syntax defined for dynamic SQL expressions. User macros don't support auxiliary arguments and are always invoked with the current instance as the only argument. This example adds a dynamic attribute called *age* which calculates the user's age with a user macro.
```php
/**
 * @entity
 */
class User {
    /**
     * @column user_id
     */
    public $id;
    
    /**
     * @type string
     */
     public $name;
    
    /**
     * @column birth_date
     * @type dt
     */
    public $birthDate;
    
    /**
     * User's age
     * @eval (as-int (diff-format (#birthDate) (now) "%y"))
     */
    public $age;
}
```

<br/>
**Conditions**

<br/>
It is also possible to associate a condition to an attribute using the **@cond** annotation. These attributes will be evaluated only if the given condition is true. Conditions must be entered as user macros. This example entity adds a property that checks if an obtained product belongs to the *software* category before executing a statement.
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
     * @type string
     */
     public $category;
    
    /**
     * @cond (== (#category) "software")
     * @stmt 'products.findSupportedOS'
     * @arg #id
     * @type obj[]
     */
    public $supportedOS;
}
```

<br/>
Exceptions
----------

<br/>
Altrough *eMapper* uses the exceptions already included in SPL, there are some special scenarios where those included in the library are used. These special scenarios are the following:

 - Query syntax error
 - Failed database connection
 - Errors when processing a mapping expression or result

<br/>
The associated exception depends on the database server currently used, althrough all of them extend from *eMapper\Exception\MapperException*.

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
Through the **sql** method we can query the database to obtain a result without further processing. Result type will depend  on the mapper class that we are using.

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

Through the **no_rows** method we can associate the execution of an auxiliary function whenever a query does not return any rows. The specified callback takes the obtained result as argument .

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
The **each** method allows us to apply a user-defined function to each of the rows returned by a query. This function will receive two arguments: the current row and the mapper instance. The example below calculates the age of each user returned by a query.

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
If a filter is applied to a single element (which do not come within a list) and the specified condition evaluates to false then NULL is returned.

<br/>
**Unescaped strings**

The *ustring* type lets you insert an unescaped string within a query. This feature requires special attention because it is possible to suffer from *SQL injection attacks* when not used properly.

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
Configuration options are values that manage a mapper object behavior during a query/statement/procedure execution. They can be configured through class methods like **type**, **cache**, etc. or can be manipulated directly with **set**. This is the list of all predefined keys available with their respective descriptions.

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
