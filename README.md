eMapper
==============

**The Extensible Data Mapper library for PHP**

<br/>
**Author**: Emmanuel Antico
<br/>
**Version**: 3.1.0

<br/>
Changelog
------------------
<br/>
2014-07-XX - Version 3.1.0 

  * Added: Entity Managers (ORM).
  * Modified: Class annotations. Now eMapper depends on emapper/annotations, a slightly modified version of minime/annotations. 
  * Modified: Dynamic SQL syntax delimiters.

<br/>
Dependencies
--------------
<br/>
- PHP >= 5.4
- [eMapper-annotations](https://github.com/emaphp/eMapper-annotations "") package
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
        "emapper/emapper" : "3.1"
    }
}
```

<br/>
About
------------

<br/>
*eMapper* is a PHP library aimed to provide a simple, powerful and highly customizable data mapping tool. It comes with some interesting features like:

- **Customized mapping**: Results can be mapped to a desired type through mapping expressions.
- **Indexation and Grouping**: Lists can be indexed or grouped together by a column value.
- **Custom types**: Developers can design their own types and custom type handlers.
- **Cache providers**: Obtained data can be stored in cache using APC or Memcache.
- **Dynamic SQL**: Queries can contain Dynamic SQL clauses writted in *eMacros*.
- **Entity Managers**: Managers provide a set of small ORM features which are common in similar frameworks.

<br/>
Introduction
-----------

<br/>
>Step 1: Pick an engine

eMapper supports SQLite, MySQL (or MariaDB if you prefer) and PostgreSQL. Creating a connection requires creating an instance of the corresponding driver.

```php
//MYSQL
use eMapper\Engine\MySQL\MySQLDriver;

$driver = new MySQLDriver('database', 'localhost', 'user', 'passw');
//...

//SQLite
use eMapper\Engine\SQLite\SQLiteDriver;

$driver = new SQLiteDriver('database.sqlite');
//...

//PostgreSQL
use eMapper\Engine\PostgreSQL\PostgreSQLDriver;

$driver = new PostgreSQLDriver('host=localhost port=5432 dbname=database user=postgres password=123456');
```

<br/>
>Step 2: The Data Mapper

Now that the connection driver is ready we create an intance of the *Mapper* class.
```php
use eMapper\Mapper;

$mapper = new Mapper($driver);
```

<br/>
>Step 3: Have fun

eMapper is not ORM-oriented but type-oriented. Everything revolves around queries an *mapping expressions*. The following examples try to give you an idea of how the data mapping engine works.

```php
//by default, results are mapped to an array of arrays
$users = $mapper->query("SELECT * FROM users");

foreach ($users as $user) {
    echo $user['name'];
    //...
}

//to indicate the desired type we overload the mapper instance by calling the type method
$username = $mapper->type('string')->query("SELECT name FROM users WHERE id = 100");

//queries can receive a variable number of arguments
$users = $mapper->query("SELECT * FROM users WHERE surname = %{s} AND id > %{i}", "Doe", 1000);

//'arr' (or 'array') returns a row as an associative array
$user = $mapper->type('arr')->query("SELECT * FROM users WHERE name = %{s}", 'emaphp');

//using 'obj' as mapping expression will convert a row to an instance of stdClass
$user = $mapper->type('obj')->query("SELECT * FROM users WHERE id = %{i}", 42);
```

<br/>
Mapping 101
-----------

<br/>
#####Simple types

```php
//mapping to an integer ('i', 'int', 'integer')
$total = $mapper->type('i')->query("SELECT COUNT(*) FROM users WHERE sex = %{s}", 'F');

//mapping to a boolean ('b', 'bool', 'boolean')
$suscribed = $mapper->type('b')->query("SELECT is_suscribed FROM users WHERE id = %{i}", 99);

//mapping to a float ('f', 'float', 'real', 'double')
$price = $mapper->type('f')->query("SELECT MAX(price) FROM products WHERE refurbished = {b}", false);

//mapping to a string ('s', 'str', 'string')
$body = $mapper->type('s')->query("SELECT message FROM messages WHERE slug = %{s}", 'emapper_rocks');

//dates ('DateTime', 'dt', 'timestamp', 'datetime'
$lastLogin = $mapper->type('dt')->query("SELECT last_login FROM users WHERE id = %{i}", 1984);

//finally, you can tell the exact column to fetch by providing a second argument
$code = $mapper->type('s', 'serial')->query("SELECT * FROM products WHERE id = %{i}", 101);
```

<br/>
#####Arrays

```php
//by default, an array contains both numeric and associative indexes
$movie = $mapper->type('array')->query("SELECT * FROM movies WHERE id = %{i}", 55);

//but you can change this by adding a second argument
use eMapper\Result\ArrayType;

//numeric only
$movie = $mapper->type('array', ArrayType::NUM)->query("SELECT * FROM movies WHERE id = %{i}", 56);

//associative only
$movie = $mapper->type('array', ArrayType::ASSOC)->query("SELECT * FROM movies WHERE id = %{i}", 57);
```

<br/>
#####Objects

```php
//mapping to a stdClass instance ('obj', 'object')
$book = $mapper->type('obj')->query("SELECT * FROM books WHERE isbn = %{s}", "9789507315428");

//using a custom class
$book = $mapper->type('obj:Acme\\Library\\Book')->query("SELECT * FROM books WHERE isbn = %{s}", "9788437604183");
```

*Note*: One important thing to remember when mapping to a structure is that values contained in columns declared using the DATE or DATETIME types are converted to instances of [DateTime](http://ar2.php.net/manual/en/class.datetime.php "").

<br/>
Lists
-----------

<br/>
#####Simple types

```php
//just add brackets at the end of the expression and you're done
$id_list = $mapper->type('int[]')->query("SELECT id FROM users");

$prices = $mapper->type('float[]')->query("SELECT price FROM products");

$isbn_list = $mapper->type('string[]')->query("SELECT isbn FROM books");

$creation_dates = $mapper->type('dt[]')->query("SELECT created_at FROM posts");

//again, a second argument could be provided to define which column to fetch
$refurbished = $mapper->type('bool[]', 'refurbished')->query("SELECT * FROM products");
```

<br/>
#####Arrays and Objects

```php
//same rule applies to arrays and objects
$users = $mapper->type('obj[]')->query("SELECT * FROM users");

//an array type could also be provided when mapping to lists
use eMapper\Result\ArrayType;

$users = $mapper->type('arr[]', ArrayType::ASSOC)->query("SELECT * FROM users");
```

<br/>
Indexation and Grouping
-----------------------

<br/>
#####Indexes
```php
//the column goes between brackets
$books = $mapper->type('array[id]')->query("SELECT * FROM books");

//a default type could be added in the following way
$products = $mapper->type('arr[id:string]')->query("SELECT * FROM products");

//make sure the column is present in the result (this shouldn't work)
$books = $mapper->type('obj[isbn]')->query("SELECT id, name, price FROM books");

//when mapping to arrays, the index should be represented appropriately
use eMapper\Result\ArrayType;

$products = $mapper->type('arr[0]', ArrayType::NUM)->query("SELECT * FROM products");
```

<br/>
#####Groups
```php
//group by a known column
$products = $mapper->type('arr<category>')->query("SELECT * FROM products");

//a type could also be specified
$books = $mapper->type('obj<publisher:string>')->query("SELECT * FROM books");

//both expressions can be mixed to obtain interesting results
$products = $mapper->type('obj<category>[id:int]')->query("SELECT * FROM products");
```

<br/>
#####Callbacks
```php
//lists could also be indexed using a closure
$products = $mapper->type('array[]')
->index_callback(function ($product) {
    //return a custom made index
    return $product['code'] . '_' . $product['id'];
})
->query("SELECT * FROM products");

// a group callback does what you expect and can also be combined with indexation
$products = $mapper->type('obj[id]')
->group_callback(function ($product) {
    return substr($product->category, 0, 3);
})
->query("SELECT * FROM products");
```

<br/>
Queries
-------


<br/>
#####Query arguments

```php
//arguments the easy way
$products = $mapper->type('obj[]')->query("SELECT * FROM products WHERE price < %{f} AND category = %{s}", 699.99, 'Laptops');

//argument by position (plus type)
$products = $mapper->type('obj[]')->query("SELECT * FROM products WHERE category = %{1} AND price < %{0:f}", 699.99, 'Laptops');
```

<br/>
#####Arrays/Objects as argument

```php
//array as parameter
$parameter = ['password' => sha1('qwerty'), 'modified_at' => new \DateTime];
$mapper->query("UPDATE users SET password = #{password}, modified_at = #{modified_at:dt} WHERE name = %{1:string}", $parameter, 'emaphp');

//syntax works with objects as well
use Acme\CMS\Comment;

$comment = new Comment();
$comment->setUserId(100);
$comment->setBody("Hello World");

$mapper->query("INSERT INTO comments (user_id, body) VALUES (#{userId}, #{body});", $comment);
```
*Note*: The syntax for array/object attributes work as long as you provide the array/object as the first argument.

<br/>
Named Queries
-------------

#####Statements

```php
//declaring a statement
$mapper->stmt("findUserByPk", "SELECT * FROM users WHERE user_id = %{i}");

//statements are invoked with the execute method
$mapper->type('obj[]')->execute('findUserByPk', 100);
```
#####Configuration

```php
//the stmt method supports a third argument with the predefined configuration for that query
use eMapper\SQL\Statement;

//declare statement
$mapper->stmt('findProductsByCategory',
              "SELECT * FROM products WHERE category = %{s}",
              Statement::type('obj[]'));

//get products as an object list
$products = $mapper->execute('findProductsByCategory', 'Audio');

//override default configuration and return result as an indexed list of arrays
$products = $mapper->type('array[id]')->execute('findProductsByCategory', 'Smartphones');
```

#####Namespaces

```php
//namespaces provide a more organized way of declaring statements
use eMapper\SQL\SQLNamespace;
use eMapper\SQL\Statement;

class UsersNamespace extends SQLNamespace {
    public function __construct() {
        //set namespace id through parent constructor
        parent::__construct('users');
        
        $this->stmt('findByPk',
                    "SELECT * FROM users WHERE id = %{i}",
                    Statement::type('obj'));
                    
        $this->stmt('findByName',
                    "SELECT * FROM users WHERE name = %{s}",
                    Statement::type('obj'));
                    
        $this->stmt('findRecent'
                    "SELECT * FROM users WHERE created_at >= subdate(NOW(), INTERVAL 3 DAY)",
                    Statement::type('obj[id:int]'));
    }
}

//add namespace
$mapper->addNamespace(new UsersNamespace());

//namespace id must be specified as a prefix
$user = $mapper->execute('users.findByPK', 4);
```

<br/>
Stored procedures
-----------------

<br/>
#####Invoking stored procedures

The *Mapper* class uses [method overloading](http://php.net//manual/en/language.oop5.overloading.php "") to translate an invokation to an unexistant method into a stored procedure call.

```php
//MySQL: CALL Users_Clean()
//PostgreSQL: SELECT Users_Clean()
$mapper->Users_Clean();

//if database prefix is set then is used as a prefix
$mapper->setPrefix('COM_');
//MySQL: CALL COM_Users_Clean()
//PostgreSQL: CALL COM_Users_Clean()
$mapper->Users_Clean();
```

<br/>
#####Mapping values
```php
//simple values con be obtained like usual
$total = $mapper->type('i')->Users_CountActive();

//engines may differ in how to treat a structured value
$user = $mapper->type('obj')->Users_FindByPK(3); //works in MySQL only

//PostgreSQL needs an additional configuration value
//SQL: SELECT * FROM Users_FindByPK(3)
$user = $mapper->type('obj')->option('proc.as_table', true)->Users_FindByPK(3);
```

<br/>
Entity Managers
---------------

<br/>
#####Entities
Writing all your queries can turn very frustating real soon. Luckily, eMapper provides a small set of ORM features that can save you a lot of time. Entity managers are objects that behave like DAOs ([Data Access Object](http://en.wikipedia.org/wiki/Data_access_object "")) for a specified class. The first step to create an entity manager is designing an entity class. The following example shows a class called *Product*. This class defines 5 attributes, each one defines its type through the *@Type* annotation. If the attribute name differs from the column name we can add a *@Column* annotation telling the exact column name. As a general rule, all entities must defined a primary key attribute. The *Product* class sets its id attribute as primary key using the *@Id* annotation.

```php
namespace Acme\Factory;

/**
 * @Entity products
 */
class Product {
    /**
     * @Id
     * @Type integer
     */
    private $id;
    
    /**
     * @Type string
     * @Column pcod
     */
    private $code;

    /**
     * @Type string
     */
    private $description;

    /**
     * @Type string
     */
    private $category;
    
    /**
     * @Type float
     */
    private $price;
    
    public function getId() {
        return $this->id;
    }
    
    public function setCode($code) {
        $this->code = $code;
    }
    
    public function getCode()  {
        return $this->code;
    }
    
    public function setDescription($description) {
        $this->description = $description;
    }

    public function getDescription() {
        return $this->description;
    }
    
    public function setCategory($category) {
        $this->category = $category;
    }

    public function getCategory(){
        return $this->category;
    }
    
    public function setPrice($price) {
        $this->price = $price;
    }

    public function getPrice() {
        return $this->price;
    }
}
```
Managers are created through the *buildManager* method in the *Mapper* class. This method expects the entity class full name. Managers are capable of getting results using filters without having to write SQL manually.

```php
//create a products manager
$products = $mapper->buildManager('Acme\\Factory\\Product');

//get by id
$product = $products->findByPk(4);

//obtain a product by code
use eMapper\Query\Attr;

$product = $products->get(Attr::code()->eq('XXX098'));

//get a list of products by a given category
$list = $products->find(Attr::category()->eq('Laptops'));

//define a filter (category = 'E-Books' AND price < 799.99)
$list = $products
->filter(Attr::category()->eq('E-Books'), Attr::price()->lt(799.99))
->find();

//OR condition (category = 'E-Books' OR price < 799.99)
use eMapper\Query\Q;

$list = $products->find(Q::where(Attr::category()->eq('E-Books'), Attr::price()->lt(799.99)));

//exclude (category <> 'Fitness')
$list = $products->exclude(Attr::category()->eq('Fitness'))->find();

//reverse condition (description NOT LIKE '%Apple%')
$list = $products->filter(Attr::description()->contains('Apple', false))->find();
```

<br/>
#####Utilities

```php
use eMapper\Query\Attr;

//getting all products indexed by id
$list = $products->index(Attr::id())->find();

//or grouped by category
$list = $products->group(Attr::category())->find();

//callbacks work as well
$list = $products
->index_callback(function ($product) {
    return $product->getId() . substr($product->getCode(), 0, 5);
})
->find();

//order and limit clauses
$list = $products->order_by(Attr::id())->limit(15)->find();

//setting the order type
$list = $products->order_by(Attr::id('ASC'), Attr::category())->find();

//count products of a given category
$total = $products->filter(Attr::category()->eq('Audio'))->count();

//average price
$avg = $products->exclude(Attr::category()->eq('Laptops'))->avg(Attr::price());

//max price (returns as integer)
```

<br/>
#####Storing objects

<br/>
#####Deleting objects


<br/>
Dynamic Attributes
-----------

<br/>
Cache
-----

<br/>
Currently, eMapper supports APC, Memcache and Memcached. Before setting a cache provider make sure the extension is correctly installed.

#####Providers
```php
//APC
use eMapper\Cache\APCProvider;

$provider = new APCProvider;
$mapper->setCacheProvider($provider);

//Memcache
use eMapper\Cache\MemcacheProvider;

$provider = new MemcacheProvider('localhost', 11211);
$mapper->setCacheProvider($provider);

//Memcached
use eMapper\Cache\MemcachedProvider;

$provider = new MemcachedProvider('mem');
$mapper->setProvider($provider);
```

#####Storing values
```php
//get users and store values with the key 'USERS_ALL' (time to live: 60 sec)
//if the key is found in cache no query takes place (write once, run forever)
$users = $mapper->cache('USERS_ALL', 60)->query("SELECT * FROM users");

//cache keys also receive query arguments
//store values with the key 'USER_6'
$user = $mapper->type('obj')->cache('USER_%{i}', 120)->query("SELECT * FROM users WHERE id = %{i}", 6);
```

<br/>
Dynamic SQL
-----------

<br/>
#####Introduction
Queries could also contain logic expressions which are evaluated againts current arguments. These expressions (or S-expressions) are written in [eMacros](https://github.com/emaphp/eMacros ""), a language based on lisphp. Dynamic expressions are included between the delimiters [? and ?]. The next example shows a query that sets the condition dynamically according to the argument type.
```sql
SELECT * FROM users WHERE [? (if (int? (%0)) 'id = %{i}' 'name = %{s}') ?]
```

```php
//add named query
$mapper->stmt('findUser', "SELECT * FROM users WHERE [? (if (int? (%0)) 'id = %{i}' 'name = %{s}') ?]");

//find by id
$user = $mapper->type('obj')->execute('findUser', 99);

//find by name
$user = $mapper->type('obj')->execute('findUser', 'emaphp');
```

<br/>
#####eMacros 101

Just to give you a basic approach of how S-expressions work here's a list of small examples. Refer to eMacros documentation for more.
```lisp
; simple math
(+ 4 10) ; 14
(- 10 4) ; 6
(* 5 3 2) ; 30

; sum first two arguments
(+ (%0) (%1))

; concat
(. "Hello" " " "World!")

; is int?
(int? 6) ; true

; is string?
(string? true) ; false

; cast to float
(as-float "65.32")

; get property value
(#description)

; get configuration value
(@default_order)

; if the else
(if (null? (#id)) "No id found!" (#id)) ; return id if is not null
```

<br/>
#####Configuration values

This example adds an ORDER clause if the configuration key 'order' is set.
```sql
SELECT * FROM users [? (if (@order?) (. 'ORDER BY ' (@order))) ?]
```

```php
//add named query
$mapper->stmt('obtainUsers', "SELECT * FROM users [? (if (@order?) (. 'ORDER BY ' (@order))) ?]");

//get all users
$mapper->type('obj[]')->execute('obtainUsers');

//the option method creates an instance with an additional configuration value
//get ordered users
$mapper->type('obj[]')->option('order', 'last_login')->execute('obtainUsers');
```

<br/>
#####Typed expressions
A value returned by a dynamic SQL expression can be associated to a type by adding the type identifier right after the first delimiter. This examples simulates a search using the LIKE operator.
```sql
SELECT * FROM users WHERE name LIKE [?string (. '%' (%0) '%') ?]
```

```php
//add named query
$mapper->stmt('searchUsers', "SELECT * FROM users WHERE name LIKE [?string (. '%' (%0) '%') ?]");

//search by name
$users = $mapper->map('obj[]')->execute('searchUsers', 'ema');
```

<br/>
License
--------------
<br/>
This code is licensed under the BSD 2-Clause license.
