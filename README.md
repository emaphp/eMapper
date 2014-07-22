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

eMapper supports SQLite, MySQL and PostgreSQL (for now). Creating a connection requires creating an instance of the corresponding driver.

```php
//MYSQL
use eMapper\Engine\MySQL\MySQLDriver;

$driver = new MySQLDriver('database', 'localhost', 'mysql', '123456');
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

//when mapping to arrays, the index should be represented appropiatedly
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

```php
//arguments the easy way
$products = $mapper->type('obj[]')->query("SELECT * FROM products WHERE price < %{f} AND category = %{s}", 699.99, 'Laptops');

//argument by position (plus type)
$products = $mapper->type('obj[]')->query("SELECT * FROM products WHERE category = %{1} AND price < %{0:f}", 699.99, 'Laptops');

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

<br/>
Named Queries
-------------

#####Statements

```php
```
#####Configuration

```php
```

#####Namespaces

```php
```

<br/>
Entity Managers
---------------

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
     * @Column desc
     */
    private $description;

    /**
     * @Type string
     */
    private $category;
    
    private $price;
    
    public function getId() {
        return $this->id;
    }
    
    public function getDescription() {
        return $this->description;
    }
    
    public function getCategory(){
        return $this->category;
    }
    
    public function getPrice() {
        return $this->price;
    }
}
```

```php
//create a products manager (a useful one)
$products = $manager->buildManager('Acme\\Factory\\Product');

//get by id
$product = $products->findByPk(4);

//get by code
use eMapper\Query\Attr;

$product = $products->get(Attr::code()->eq('XXX098'));
```

<br/>
Stored procedures
-----------------

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
Queries could also contain logic expressions that are evaluated againts current arguments. These expressions (or S-expressions) are written in [eMacros](https://github.com/emaphp/eMacros ""), a language based on lisphp. Dynamic expressions are included between the delimiters [? and ?]. The following query sets the condition according to argument type.
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

//get ordered users
$mapper->type('obj[]')->option('order', 'last_login')->execute('obtainUsers');
```

<br/>
#####Typed expressions
A value returned by a dynamix sql expression can be associated to a type by adding the type indentifier right after the first delimiter. This examples simulates a search using the LIKE operator.
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
Entities: Dynamic Attributes
-----------

<br/>
License
--------------
<br/>
This code is licensed under the BSD 2-Clause license.
